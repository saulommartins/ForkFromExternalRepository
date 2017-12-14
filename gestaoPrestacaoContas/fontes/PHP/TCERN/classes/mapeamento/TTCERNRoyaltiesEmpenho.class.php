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
 * Classe de mapeamento da tabela tcern.royalties_empenho
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id$
 * @author
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCERNRoyaltiesEmpenho extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author
     */
    public function TTCERNRoyaltiesEmpenho()
    {
        parent::Persistente();
        $this->setTabela('tcern.royalties_empenho');

        $this->setCampoCod('cod_empenho, cod_entidade, exercicio, cod_royalties');
        $this->setComplementoChave('');

        $this->AddCampo('cod_empenho'            , 'integer', true  , ''    , false, true);
        $this->AddCampo('cod_entidade'           , 'integer', true  , ''    , false, true);
        $this->AddCampo('exercicio'              , 'varchar', true  , '4'   , false, true);
        $this->AddCampo('cod_royalties'             , 'integer', true  , ''    , false, true);
    }
}
