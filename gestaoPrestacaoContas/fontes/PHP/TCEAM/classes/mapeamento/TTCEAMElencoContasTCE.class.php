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
 * Classe de mapeamento da tabela tceam.elenco_contas_tce
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id$
 * @author eduardo.schitz@cnm.org.br
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEAMElencoContasTCE extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author eduardo.schitz@cnm.org.br
     */
    public function TTCEAMElencoContasTCE()
    {
        parent::Persistente();
        $this->setTabela('tceam.elenco_contas_tce');

        $this->setComplementoChave('cod_elenco, exercicio');

        $this->AddCampo('cod_elenco'   , 'integer', true, ''  , true , false);
        $this->AddCampo('exercicio'    , 'varchar', true, '4' , true , false);
        $this->AddCampo('seq'          , 'integer', true, ''  , false, false);
        $this->AddCampo('cod_conta_tce', 'varchar', true, '25', false, false);
        $this->AddCampo('descricao'    , 'varchar', true, ''  , false, false);
        $this->AddCampo('nivel'        , 'varchar', true, '1' , false, false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "SELECT *                                             \n";
        $stSql .= "  FROM tceam.elenco_contas_tce                       \n";
        $stSql .= " WHERE exercicio = '".$this->getDado('exercicio')."'   \n";

        return $stSql;

    }

}
