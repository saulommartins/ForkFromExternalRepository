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
    * Classe de mapeamento da tabela DIVIDA.PARCELA_CALCULO
    * Data de Criação: 16/02/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaParcelaCalculo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.1  2007/02/23 18:49:01  cercato
alteracoes em funcao das mudancas no ER.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATDividaParcelaCalculo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaParcelaCalculo()
    {
        parent::Persistente();
        $this->setTabela('divida.parcela_calculo');

        $this->setCampoCod('');
        $this->setComplementoChave('num_parcelamento, num_parcela, cod_calculo');

        $this->AddCampo('num_parcelamento','integer',true,'',true,true);
        $this->AddCampo('num_parcela','integer',true,'',true,true);
        $this->AddCampo('cod_calculo','integer',true,'',true,true);

        $this->AddCampo('vl_credito','numeric',true,'',false,false);

    }

}// end of class
?>
