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
    * Classe de mapeamento da tabela PPA.PROGRAMA_ORGAO_RESPONSAVEL
    * Data de Criação: 03/10/2008

    * @author Analista: Bruno Ferreira
    * @author Desenvolvedor: Jânio Eduardo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPAProgramaOrgaoResponsavel extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPPAProgramaOrgaoResponsavel()
    {
        parent::Persistente();

        $this->setTabela('ppa.programa_orgao_responsavel');

        $this->setCampoCod('cod_programa');
        $this->setComplementoChave('exercicio,num_orgao');

        $this->AddCampo('cod_programa', 'integer', true, '', true, false);
        $this->AddCampo('timestamp_programa_dados', 'timestamp', true, '', false, true);
        $this->AddCampo('exercicio', 'integer', true, '', false, true);
        $this->AddCampo('num_orgao', 'interger', true, '', true, false);
    }

    public function recuperaNomOrgao(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaNomOrgao", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaNomOrgao()
    {
        $stSql = "    SELECT DISTINCT p.cod_programa                                            \n";
        $stSql.= "         , p.cod_ppa                                                          \n";
        $stSql.= "         , p.num_programa                                                     \n";
        $stSql.= "         , por.num_orgao                                                      \n";
        $stSql.= "         , por.exercicio                                                  \n";
        $stSql.= "         , oa.nom_orgao                                                       \n";
        $stSql.= "      FROM ppa.programa as p                                                  \n";
        $stSql.= "INNER JOIN ppa.programa_dados as pd                                           \n";
        $stSql.= "        ON p.ultimo_timestamp_programa_dados = pd.timestamp_programa_dados    \n";
        $stSql.= "INNER JOIN ppa.programa_orgao_responsavel as por                              \n";
        $stSql.= "        ON p.ultimo_timestamp_programa_dados = por.timestamp_programa_dados   \n";
        $stSql.= " LEFT JOIN ppa.programa_temporario_vigencia as ptv                            \n";
        $stSql.= "        ON p.ultimo_timestamp_programa_dados = ptv.timestamp_programa_dados   \n";
        $stSql.= "INNER JOIN orcamento.orgao as oa                                              \n";
        $stSql.= "           ON por.num_orgao = oa.num_orgao                                    \n";
        $stSql.= "          AND por.exercicio = oa.exercicio                                \n";

        return $stSql;
    }
}

?>
