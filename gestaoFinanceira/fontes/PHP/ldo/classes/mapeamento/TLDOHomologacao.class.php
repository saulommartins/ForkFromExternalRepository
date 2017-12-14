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
 * Classe Mapeameto do 02.10.01 - Homologar LDO
 * Data de Criação: 06/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Henrique Boaventura
 * @package GF
 * @subpackage LDO
 *
 * $Id: TLDOHomologacao.class.php 61768 2015-03-03 13:08:43Z michel $
 */

class TLDOHomologacao extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela('ldo.homologacao');

        $this->setCampoCod        ('cod_ppa');
        $this->setComplementoChave('ano,timestamp');

        $this->addCampo('cod_ppa'          , 'integer'  , true, '' , true , true);
        $this->addCampo('ano'              , 'character', true, '1', true , true);
        $this->addCampo('timestamp'        , 'timestamp', true, '' , true , false);
        $this->addCampo('cod_norma'        , 'integer'  , true, '' , false, true);
        $this->addCampo('numcgm_veiculo'   , 'integer'  , true, '' , false, true);
        $this->addCampo('cod_periodicidade', 'integer'  , true, '' , false, true);
        $this->addCampo('dt_encaminhamento', 'date'     , true, '' , false, false);
        $this->addCampo('dt_devolucao'     , 'date'     , true, '' , false, false);
        $this->addCampo('nro_protocolo'    , 'character', true, '9', false, false);
    }

    public function recuperaLDOPorPPA(&$rsPPAs, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $stSql  = "\n     SELECT ppa.cod_ppa";
        $stSql .= "\n          , ldo.ano";
        $stSql .= "\n          , (to_number(ppa.ano_inicio, '9999') + to_number(ldo.ano, '9') - 1) AS exercicio";
        $stSql .= "\n       FROM ldo.ldo";
        $stSql .= "\n INNER JOIN ldo.homologacao";
        $stSql .= "\n         ON homologacao.cod_ppa = ldo.cod_ppa";
        $stSql .= "\n        AND homologacao.ano     = ldo.ano";
        $stSql .= "\n INNER JOIN ppa.ppa";
        $stSql .= "\n         ON ppa.cod_ppa = ldo.cod_ppa";

        if ($this->getDado('cod_ppa')) {
            $stSql .= "\n        AND ppa.cod_ppa = ".$this->getDado('cod_ppa');
        }

        return $this->executaRecuperaSql($stSql, $rsPPAs, $stFiltro, $stOrdem, $boTransacao);
    }
    
    function recuperaLDOPorAnoPPANorma(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : $stOrdem;
        $stSql = $this->montaRecuperaLDOPorAnoPPANorma().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaLDOPorAnoPPANorma()
    {
        if($this->getDado('exercicio'))
            $inExercicio = $this->getDado('exercicio');
        else
            $inExercicio = Sessao::getExercicio();

        $stSql = "SELECT ppa.cod_ppa                                                                                                                                        \n";
        $stSql.= "     , ppa.ano_inicio                                                                                                                                     \n";
        $stSql.= "     , ppa.ano_final                                                                                                                                      \n";
        $stSql.= "     , ppa.timestamp                                                                                                                                      \n";
        $stSql.= "     , ppa.ano_inicio||' a '||ppa.ano_final AS periodo                                                                                                    \n";
        $stSql.= "     , ppa_publicacao.timestamp AS dt_homologacao_ppa                                                                                                     \n";
        $stSql.= "     , ppa_publicacao.cod_norma AS cod_norma_ppa                                                                                                          \n";
        $stSql.= "     , tipo_norma.nom_tipo_norma||' '||LPAD(norma.num_norma, 6, '0')||'/'||norma.exercicio||' - '||norma.nom_norma AS descricao_norma_ppa                 \n";
        $stSql.= "     , (".$inExercicio." - ppa.ano_inicio::integer + 1) AS ano_ldo                                                                                        \n";
        $stSql.= "     , homologacao.cod_norma AS cod_norma_ldo                                                                                                             \n";
        $stSql.= "     , tipo_norma_ldo.nom_tipo_norma||' '||LPAD(norma_ldo.num_norma, 6, '0')||'/'||norma_ldo.exercicio||' - '||norma_ldo.nom_norma AS descricao_norma_ldo \n";
        $stSql.= "     , homologacao.timestamp::date AS dt_homologacao_ldo                                                                                                  \n";
        $stSql.= "  FROM ppa.ppa                                                                                                                                            \n";
        $stSql.= "  JOIN ppa.ppa_publicacao                                                                                                                                 \n";
        $stSql.= "    ON ppa_publicacao.cod_ppa = ppa.cod_ppa                                                                                                               \n";
        $stSql.= "   AND ppa_publicacao.timestamp = (SELECT MAX(ppa_publicacao.timestamp) FROM ppa.ppa_publicacao WHERE ppa_publicacao.cod_ppa = ppa.cod_ppa)               \n";
        $stSql.= "   AND ".$inExercicio." BETWEEN ppa.ano_inicio::INTEGER AND ppa.ano_final::INTEGER                                                                        \n";
        $stSql.= "  JOIN normas.norma                                                                                                                                       \n";
        $stSql.= "    ON norma.cod_norma = ppa_publicacao.cod_norma                                                                                                         \n";
        $stSql.= "  JOIN normas.tipo_norma                                                                                                                                  \n";
        $stSql.= "    ON tipo_norma.cod_tipo_norma = norma.cod_tipo_norma                                                                                                   \n";
        $stSql.= "  JOIN ldo.homologacao                                                                                                                                    \n";
        $stSql.= "    ON homologacao.cod_ppa = ppa.cod_ppa                                                                                                                  \n";
        $stSql.= "   AND homologacao.ano = (".$inExercicio." - ppa.ano_inicio::integer + 1)::varchar                                                                        \n";
        $stSql.= "   AND homologacao.timestamp = (select max(timestamp) from ldo.homologacao as lh where lh.cod_ppa = homologacao.cod_ppa and lh.ano = homologacao.ano)     \n";
        $stSql.= "  JOIN normas.norma AS norma_ldo                                                                                                                          \n";
        $stSql.= "    ON norma_ldo.cod_norma = homologacao.cod_norma                                                                                                        \n";
        $stSql.= "  JOIN normas.tipo_norma AS tipo_norma_ldo                                                                                                                \n";
        $stSql.= "    ON tipo_norma_ldo.cod_tipo_norma = norma_ldo.cod_tipo_norma                                                                                           \n";
        $stSql.= " WHERE ppa.fn_verifica_homologacao(ppa.cod_ppa) = TRUE                                                                                                    \n";

        return $stSql;
    }

}
