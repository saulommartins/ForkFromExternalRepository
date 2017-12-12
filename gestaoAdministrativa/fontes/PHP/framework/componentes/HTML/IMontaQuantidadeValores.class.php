<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Arquivo de popup de busca de Item do catálogo
    * Data de Criação: 07/06/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage

    $Revision: 24521 $
    $Name$
    $Author: bruce $
    $Date: 2007-08-06 12:50:57 -0300 (Seg, 06 Ago 2007) $

    * Casos de uso: uc-01.01.00
*/

/*
$Log$
Revision 1.7  2007/08/06 15:50:57  bruce
Bug#9712#

Revision 1.6  2007/07/20 16:13:53  hboaventura
Bug#9712#

*/
include_once ( CLA_OBJETO );

class  IMontaQuantidadeValores extends Objeto
{
    public $obHidden;
    public $obValorUnitario;
    public $obQuantidade;
    public $obValorTotal;

    public function IMontaQuantidadeValores()
    {
        parent::Objeto();
        include_once(CLA_VALOR_UNITARIO);
        include_once(CLA_QUANTIDADE);
        include_once(CLA_VALOR_TOTAL);

        $this->obHidden  = new Hidden();
        $this->obHidden->setName("hdnIMontaValoresCompras");
        $this->obHidden->setId( $this->obHidden->getName() );

        $this->obValorUnitario = new ValorUnitario();
        $this->obValorUnitario->setValue('0,0000');

        $this->obQuantidade = new Quantidade();
        $this->obQuantidade->setValue('0,0000');
        $this->obQuantidade->setSize (14);
        $this->obQuantidade->setMaxLength(13);
        $this->obQuantidade->setDefinicao('NUMERIC');

        $this->obValorTotal    = new ValorTotal();
        $this->obValorTotal->setValue('0,0000');

    }

    public function geraFormulario(&$obFormulario)
    {
        $stJsVerificaTotal = "
            if ( parseFloat(((document.getElementById('".$this->obValorTotal->getID()."').value).replace(/[.]/g,'')).replace(',','.')) == 0 ) {
                document.getElementById('".$this->obValorTotal->getID()."').value = '';
            }
        ";

        $stJsVerificaUni = "
            if ( parseFloat(((document.getElementById('".$this->obValorUnitario->getID()."').value).replace(/[.]/g,'')).replace(',','.')) == 0 ) {
                document.getElementById('".$this->obValorUnitario->getID()."').value = '';
            }
        ";

        $stJsVerificaQuantidade = "
            if ( parseFloat(((document.getElementById('".$this->obQuantidade->getID()."').value).replace(/[.]/g,'')).replace(',','.')) == 0 ) {
                document.getElementById('".$this->obQuantidade->getID()."').value = '';
            }
        ";

        $stJsCalculoUn = "
            var inValorQuantidadeFormatado = document.getElementById('".$this->obQuantidade->getID()."').value;
                inValorQuantidadeFormatado = inValorQuantidadeFormatado.replace(/[.]/g,'');
                inValorQuantidadeFormatado = inValorQuantidadeFormatado.replace(',','.');

            var inValorUnitarioFormatado = document.getElementById('".$this->obValorUnitario->getID()."').value;
                inValorUnitarioFormatado = inValorUnitarioFormatado.replace(/[.]/g,'');
                inValorUnitarioFormatado = inValorUnitarioFormatado.replace(',','.');

            var inValorTotal = inValorQuantidadeFormatado * inValorUnitarioFormatado;
                inValorTotal = inValorTotal.toFixed(".$this->obValorUnitario->getDecimais().");
                inValorTotal = inValorTotal.replace('.',',');

            document.getElementById('".$this->obValorTotal->getID()."').value = inValorTotal;
            mascaraNumerico(".$this->obValorTotal->getID().", ".$this->obValorTotal->getMaxLength().", ".$this->obValorTotal->getDecimais().", event, true );
        ";

        $stJsCalculoTot ="
            var inValorUnitarioFormatado = document.getElementById('".$this->obValorUnitario->getID()."').value;
                inValorUnitarioFormatado = inValorUnitarioFormatado.replace(/[.]/g,'');
                inValorUnitarioFormatado = inValorUnitarioFormatado.replace(',','.');

            var valorTotal = document.getElementById('".$this->obValorTotal->getID()."').value;
                valorTotal = valorTotal.replace(/[.]/g,'');
                valorTotal = valorTotal.replace(',','.');

            var quantidade = document.getElementById('".$this->obQuantidade->getID()."').value;
                quantidade = quantidade.replace(/[.]/g,'');
                quantidade = quantidade.replace(',','.');

            if ( (valorTotal > 0) && (quantidade > 0) ) {
                var calculo = valorTotal / quantidade;

                calculo = calculo.toFixed(".$this->obValorTotal->getDecimais().");
                calculo = calculo.replace('.',',');

                document.getElementById('".$this->obValorUnitario->getID()."').value = calculo;
                mascaraNumerico(".$this->obValorUnitario->getID().", ".$this->obValorUnitario->getMaxLength().", ".$this->obValorUnitario->getDecimais().", event, true );
            } else {
                if ( (valorTotal > 0) && (inValorUnitarioFormatado > 0) ) {
                    var calculo = valorTotal / inValorUnitarioFormatado;
                    calculo = calculo.toFixed(".$this->obValorTotal->getDecimais().");
                    calculo = calculo.replace('.',',');

                    document.getElementById('".$this->obQuantidade->getID()."').value = calculo;
                } else {
                    document.getElementById('".$this->obValorUnitario->getID()."').value = parseToMoeda(0,2);
                }
            }
        ";

        $stJsQuantidade = "if (document.getElementById('".$this->obHidden->getID()."').value == 'total') {";
        $stJsQuantidade.= $stJsCalculoTot;
        $stJsQuantidade.= "} else {                                                                       ";
        $stJsQuantidade.= $stJsCalculoUn;
        $stJsQuantidade.= "}                                                                            ";

        $this->obValorUnitario->obEvento->setOnChange($stJsCalculoUn);
        $this->obValorUnitario->obEvento->setOnClick($stJsVerificaUni);
        $this->obQuantidade->obEvento->setOnChange($stJsQuantidade);
        $this->obQuantidade->obEvento->setOnClick($stJsVerificaQuantidade);
        $this->obValorTotal->obEvento->setOnChange($stJsCalculoTot);
        $this->obValorTotal->obEvento->setOnClick($stJsVerificaTotal);

        $obFormulario->addHidden    ( $this->obHidden );
        $obFormulario->addComponente( $this->obValorUnitario );
        $obFormulario->addComponente( $this->obQuantidade );
        $obFormulario->addComponente( $this->obValorTotal );
    }
}
?>
