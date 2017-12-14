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
    * Classe de mapeamento da tabela tceal.publicacao_rgf
    * Data de Criação: 05/05/2016

    * @author Analista: Ane Caroline
    * @author Desenvolvedor: Lisiane da Rosa Morais

    * $Id: TTCEALPublicacaoRREO.class.php 65345 2016-05-13 18:07:34Z michel $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEALPublicacaoRREO extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela("tceal.publicacao_rreo");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio , cod_entidade , numcgm , dt_publicacao');

        $this->AddCampo('cod_entidade'  ,'integer',true ,''   ,true, true);
        $this->AddCampo('exercicio'     ,'char'   ,true ,'4'  ,true, true);
        $this->AddCampo('numcgm'        ,'integer',true ,''   ,true, true);
        $this->AddCampo('dt_publicacao' ,'date'   ,true ,''   ,true, false);
        $this->AddCampo('num_publicacao','integer',false,''   ,false,false);
        $this->AddCampo('observacao'    ,'varchar',false,'80' ,false,false);
    }

    function recuperaVeiculosPublicacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false) ? " ORDER BY ".$stOrdem : $stOrdem;
        $stSql = $this->montaRecuperaVeiculosPublicacao().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaVeiculosPublicacao()
    {
        $stSql  = "SELECT publicacao_rreo.exercicio 
                        , publicacao_rreo.cod_entidade
                        , to_char( publicacao_rreo.dt_publicacao, 'dd/mm/yyyy' ) as dt_publicacao
                        , publicacao_rreo.numcgm as num_veiculo
                        , publicacao_rreo.num_publicacao
                        , publicacao_rreo.observacao
                        , sw_cgm.nom_cgm as nom_veiculo
                     FROM tceal.publicacao_rreo
               INNER JOIN sw_cgm
                       ON sw_cgm.numcgm = publicacao_rreo.numcgm
                    WHERE exercicio = '".$this->getDado('exercicio')."'
                      AND cod_entidade =".$this->getDado('cod_entidade');
        return $stSql;
    }

    function recuperaExportacaoRREO(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false) ? " ORDER BY ".$stOrdem : $stOrdem;

        $stSql = $this->montaRecuperaExportacaoRREO().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaExportacaoRREO()
    {
        $stSql  = "SELECT ( SELECT PJ.cnpj
                              FROM orcamento.entidade
                        INNER JOIN sw_cgm_pessoa_juridica AS PJ
                                ON PJ.numcgm = entidade.numcgm
                             WHERE entidade.exercicio    = '".$this->getDado('stExercicio')."'
                               AND entidade.cod_entidade = ".$this->getDado('inEntidade')."
                          ) AS cod_und_gestora
                        , ( SELECT lpad(COALESCE(valor,'0')::varchar,4,'0') AS VALOR
                              FROM administracao.configuracao_entidade
                             WHERE configuracao_entidade.cod_modulo   = 62
                               AND configuracao_entidade.exercicio    = '".$this->getDado('stExercicio')."'
                               AND configuracao_entidade.parametro like 'tceal_configuracao_unidade_autonoma'
                               AND configuracao_entidade.cod_entidade = ".$this->getDado('inEntidade')."
                          ) AS codigo_ua
                        , publicacao_rreo.exercicio 
                        , publicacao_rreo.cod_entidade
                        , to_char( publicacao_rreo.dt_publicacao, 'dd/mm/yyyy' ) AS dt_publicacao
                        , publicacao_rreo.numcgm AS num_veiculo
                        , publicacao_rreo.num_publicacao
                        , publicacao_rreo.observacao
                        , sw_cgm.nom_cgm AS nom_veiculo
                     FROM tceal.publicacao_rreo
               INNER JOIN sw_cgm
                       ON sw_cgm.numcgm = publicacao_rreo.numcgm
                    WHERE exercicio = '".$this->getDado('stExercicio')."'
                      AND cod_entidade = ".$this->getDado('inEntidade')."
                      AND ( publicacao_rreo.dt_publicacao
                            BETWEEN TO_DATE('".$this->getDado('dtInicial')."','dd/mm/yyyy')
                                AND TO_DATE('".$this->getDado('dtFinal')."','dd/mm/yyyy')
                          )
        ";

        return $stSql;
    }
}
