import store from "@/store";


enum ContentType {
    JSON = 'application/json', 
}

export default interface Headers {
    'Content-Type': ContentType
}

enum RequestMethod {
    POST = "POST",
    GET = "GET",
    PUT = "PUT",
    DELETE = "DELETE"
}

export default interface RequestConfig  {
    action: string,
    method: RequestMethod,
    headers: Headers
}

export enum HTTPStatusCode {
    BAD_REQUEST = 400,
    GONE = 410,
    CONFLICT = 409,
    NOT_FOUND = 404,
    UNAUTHORIZED = 401
}


export  class HTTPError extends Error {
    statusCode: HTTPStatusCode;
    constructor(message: string, statusCode: HTTPStatusCode) {
        super(message);
        this.statusCode = statusCode;
    }
}






export class FetchService {

    public static async get(action: string):  Promise<Response>{
        const response = fetch(action, {
            method: RequestMethod.GET
        });
        return response;
    }

    public static async post(action: string, body: { [key: string]: any }) : Promise<Response> {
        console.log(body);
        const headers = new Headers({
            'Content-Type': 'application/json'
        });
        let CSRFToken = store.getters['auth/getCSRFToken'];
        if (CSRFToken == null) {
            const response = await this.get('/csrf');
            if (!response.ok) throw Error('Couldn\'t get CSRF Token from server');
            const responseJSON = await response.json();
            CSRFToken = responseJSON.CSRFToken;
            store.dispatch('setCSRFToken', CSRFToken);
        }
        headers.append('CSRF-TOKEN', CSRFToken);
        const response = fetch(action, {
            headers: headers,
            method: 'POST',
            body: JSON.stringify(body)
        });
        return response;
    }

    
}