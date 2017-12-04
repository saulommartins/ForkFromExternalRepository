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
/*
 * Classe de mapeamento da tabela tceam.tipo_documento_diverso
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id$
 * @author eduardo.schitz@cnm.org.br
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEAMTipoDocumentoDiverso extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author eduardo.schitz@cnm.org.br
     */
    public function TTCEAMTipoDocumentoDiverso()
    {
        parent::Persistente();
        $this->setTabela('tceam.tipo_documento_diverso');

        $this->setCampoCod('cod_tipo_documento_diverso');

        $this->AddCampo('cod_tipo_documento_diverso', 'integer', true , ''    , true , false);
        $this->AddCampo('cod_documento'             , 'integer', true , ''    , false, true);
        $this->AddCampo('numero'                    , 'varchar', false, '10'  , false, false);
        $this->AddCampo('data'                      , 'date'   , false, ''    , false, false);
        $this->AddCampo('descricao'                 , 'varchar', false, '120' , false, false);
        $this->AddCampo('nome_documento'            , 'varchar', false, '120' , false, false);
        $this->AddCampo('vl_comprometido'           , 'numeric', false, '14,2', false, false);
        $this->AddCampo('vl_total'                  , 'numeric', false, '14,2', false, false);

    }
}
