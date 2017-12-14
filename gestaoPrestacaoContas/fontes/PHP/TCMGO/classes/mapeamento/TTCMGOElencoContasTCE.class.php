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

class TTCMGOElencoContasTCE extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     * @author eduardo.schitz@cnm.org.br
     */
    public function TTCMGOElencoContasTCE()
    {
        parent::Persistente();
        $this->setTabela('tcmgo.plano_contas_tcmgo');

        $this->setComplementoChave('cod_conta, exercicio');

        $this->AddCampo('cod_plano' , 'integer', true, ''  , true , false);
        $this->AddCampo('exercicio' , 'varchar', true, '4' , true , false);
        $this->AddCampo('estrutural', 'varchar', true, '16', false, false);
        $this->AddCampo('titulo'    , 'varchar', true, '120'  , false, false);
        $this->AddCampo('natureza'  , 'varchar', true, '1' , false, false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "SELECT plano_contas_tcmgo.cod_plano AS cod_plano_tcmgo   \n";
        $stSql .= "     , plano_contas_tcmgo.exercicio                      \n";
        $stSql .= "     , plano_contas_tcmgo.estrutural                     \n";
        $stSql .= "     , plano_contas_tcmgo.titulo                         \n";
        $stSql .= "     , plano_contas_tcmgo.natureza                       \n";
        $stSql .= "  FROM tcmgo.plano_contas_tcmgo                          \n";
        $stSql .= " WHERE plano_contas_tcmgo.exercicio = '".$this->getDado('exercicio')."' \n";
        if ($this->getDado('cod_estrutural') != '') {
            $stSql .= " AND plano_contas_tcmgo.estrutural LIKE '".$this->getDado('cod_estrutural')."%' \n";
        }
        $stSql .= " ORDER BY plano_contas_tcmgo.estrutural ASC \n";

        return $stSql;

    }

}
