<x-mail::message>
# Thank You for Your Purchase!

Dear **{{ $customerName }}**,
{{-- Dear **Customer** --}}

We truly appreciate your support and for choosing **{{ config('app.name') }}** for your tech-inspired T-shirts! 

Your order is currently being processed, and we are working to ensure everything is perfect for you. Once your order has been shipped, we will send you another email with the tracking number so you can keep an eye on its journey to your doorstep.

If you have any questions in the meantime, feel free to contact us at [support@d-shirts.com](mailto:support@d-shirts.com).

Thank you for being a valued customer!

Best regards,  
The **{{ config('app.name') }}** Team
</x-mail::message>
