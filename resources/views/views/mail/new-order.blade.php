<x-mail::message>
# Hi there, great news! 🎉

You just made a sale! Here are the details of the new order:

## Customer Details
- **Name:** {{ $orderDetails['customerName'] }}
- **Email:** {{ $orderDetails['customerEmail'] }}
- **Country:** {{ $orderDetails['customerCountry'] }}

## Order Summary
- **Total T-Shirts:** {{ $orderDetails['totalTshirts'] }}
- **Total Amount:** ${{ number_format($orderDetails['totalAmount'], 2) }}

## T-Shirts Ordered:
<x-mail::table>
| Title                                   | Price   | Size  | Quantity |
| --------------------------------------- | -------:|:-----:| --------:|
@foreach ($tshirts as $tshirt)
| {{ $tshirt['title'] }}                  | ${{ number_format($tshirt['price'], 2) }} | {{ $tshirt['size'] }} | {{ $tshirt['quantity'] }} |
@endforeach
</x-mail::table>

Thanks for keeping up the great work!  
{{ config('app.name') }}
</x-mail::message>
