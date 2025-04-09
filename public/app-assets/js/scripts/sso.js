;
    export let ssoData = null;
    export let ssoToken = null;

    export function setSsoData(data) {
        if (!ssoData) {  // Only allow setting it once
            ssoData = data;
        }
    }

    export function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/; Secure; SameSite=None";
    }



    export async function getUserData() {
        // Send the message and wait for the response
        const key = await new Promise((resolve) => {
            const listener = ({ data }) => {
                if (data.message === "REQUEST_USER_DATA_RESPONSE") {
                    data.payload='U2FsdGVkX1+0g92YlcngV1BH70WUrpYjNtLB1DHcnHCUioRUFmNlnxh0XIVZE82cWCnno+etWgOrBCn1D3OKzEEU1bQ43BQhLFw5h+1IFySPKtA+n9OFltKHtda8Gq3DXcpEBmfHukHEUaw65/rkLBAxVHJviLYtcgr8LavCOSISrwGTqCZITrnQNpZ1A/WCG8/bw2vNYD12xhe9AkI2nlRR6mANe2h1vWVcxCnI21zGVUL7mhDTzs3XTcalkMIXpSGeKog/bkyhVr7iZIk3vY/7Ja/F2DkOgkY1W6oPsPw=';
                    ssoToken = data.payload;
                    setCookie('ghlAuthorization',ssoToken,30);

                    resolve(data.payload);
                    window.removeEventListener("message", listener); // Remove event listener once we get the response
                }
            };
            window.addEventListener("message", listener);
            window.parent.postMessage({ message: "REQUEST_USER_DATA" }, "*");
        });

        try {

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            // Send the key for decryption
            const res = await fetch('/decrypt-sso', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ key, _token: csrfToken })
            });
            console.log(res);
            if (!res.ok) {
                toastr.error("Failed to authenticate user");
                throw new Error('Failed to authenticate user');
            }

            toastr.success("authenticated user");

            return await res.json(); // Return the decrypted data

        } catch (error) {
            toastr.error("Unable to authenticate user:", error);
            throw new Error("Unable to authenticate user");
        }
    }
