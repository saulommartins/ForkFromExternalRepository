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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_DOCUMENTO_PARCELA
    * Data de Criação: 29/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaDocumentoParcela.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.6  2007/08/14 15:11:56  cercato
adicionando exercicio em funcao de alteracao na base de dados.

Revision 1.5  2007/04/24 18:54:36  cercato
inserindo campo novo na tabela

Revision 1.4  2007/02/09 18:26:49  cercato
correcoes para divida.cobranca

Revision 1.3  2006/10/06 17:03:21  dibueno
inserção das chaves da tabela

Revision 1.2  2006/10/05 12:11:50  dibueno
Alterações nas colunas da tabela

Revision 1.1  2006/09/29 17:30:46  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATDividaDocumentoParcela extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaDocumentoParcela()
    {
        parent::Persistente();
        $this->setTabela('divida.documento_parcela');

        $this->setCampoCod('');
        $this->setComplementoChave('num_parcelamento, num_parcela');

        $this->AddCampo('num_parcelamento','integer',true,'',true,true);
        $this->AddCampo('cod_tipo_documento','integer',true,'',true,true);
        $this->AddCampo('cod_documento','integer',true,'',true,true);
        $this->AddCampo('num_parcela','integer',true,'',false,true);
    }

}// end of class

?>
