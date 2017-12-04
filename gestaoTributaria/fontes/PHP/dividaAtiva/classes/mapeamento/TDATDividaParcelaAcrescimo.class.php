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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_PARCELA_ACRESCIMO
    * Data de Criação: 03/10/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaParcelaAcrescimo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.5  2007/02/23 18:49:01  cercato
alteracoes em funcao das mudancas no ER.

Revision 1.4  2007/02/09 18:27:17  cercato
correcoes para divida.cobranca

Revision 1.3  2006/10/06 17:03:32  dibueno
inserção das chaves da tabela

Revision 1.2  2006/10/05 14:42:18  dibueno
Alterações nas colunas da tabela

Revision 1.1  2006/10/03 17:52:18  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATDividaParcelaAcrescimo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaParcelaAcrescimo()
    {
        parent::Persistente();
        $this->setTabela('divida.parcela_acrescimo');

        $this->setCampoCod('');
        $this->setComplementoChave('num_parcelamento, num_parcela');

        $this->AddCampo('num_parcelamento','integer',true,'',true,false);
        $this->AddCampo('num_parcela','integer',true,'',true,true);

        $this->AddCampo('cod_acrescimo','integer',false,'',true,true);
        $this->AddCampo('cod_tipo','integer',false,'',true,true);

        $this->AddCampo('vlracrescimo','numeric',false,'',false,false);

    }

}// end of class
?>
