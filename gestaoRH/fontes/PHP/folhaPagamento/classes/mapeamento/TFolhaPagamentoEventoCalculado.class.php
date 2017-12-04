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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.EVENTO_CALCULADO
    * Data de Criação: 05/12/2005

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.09

    $Id: TFolhaPagamentoEventoCalculado.class.php 65090 2016-04-22 17:09:57Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.EVENTO_CALCULADO
  * Data de Criação: 05/12/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoEventoCalculado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoEventoCalculado()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.evento_calculado');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_evento,cod_registro,timestamp_registro');

    $this->AddCampo('cod_evento','integer',true,'',true             ,'TFolhaPagamentoUltimoRegistroEvento');
    $this->AddCampo('cod_registro','integer',true,'',true           ,'TFolhaPagamentoUltimoRegistroEvento');
    $this->AddCampo('timestamp_registro','timestamp',true,'',true   ,'TFolhaPagamentoUltimoRegistroEvento','timestamp');
    $this->AddCampo('valor','numeric',true,'15,2',false,false);
    $this->AddCampo('quantidade','numeric',true,'15,2',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT *                                                                                                                             \n";
    $stSql .= "  FROM (SELECT max(registro_evento_periodo.cod_contrato) as cod_contrato                                                             \n";
    $stSql .= "             , contrato_servidor_periodo.cod_periodo_movimentacao                                                                    \n";
    $stSql .= "             , registro_evento.cod_registro                                                                                          \n";
    $stSql .= "             , evento_calculado.cod_evento                                                                                           \n";
    $stSql .= "             , evento_calculado.timestamp_registro                                                                                   \n";
    $stSql .= "             , evento_calculado.valor                                                                                                \n";
    $stSql .= "             , contrato.registro                                                                                                     \n";
    $stSql .= "             , max(sw_cgm.numcgm) as numcgm                                                                                          \n";
    $stSql .= "             , max(sw_cgm.nom_cgm) as nom_cgm                                                                                        \n";
    $stSql .= "             , max(cargo.descricao) as funcao                                                                                        \n";
    $stSql .= "             , max(contrato_servidor_especialidade_funcao.descricao) as especialidade                                                                         \n";
    $stSql .= "             , max(recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01')) as lotacao                                                                                       \n";
    $stSql .= "             , max(vw_orgao_nivel.orgao) as num_lotacao                                                                              \n";
    $stSql .= "          FROM folhapagamento.evento_calculado                                                                                       \n";
    $stSql .= "             , folhapagamento.registro_evento                                                                                        \n";
    $stSql .= "             , folhapagamento.registro_evento_periodo                                                                                \n";
    $stSql .= "             , folhapagamento.contrato_servidor_periodo                                                                              \n";
    $stSql .= "             , pessoal.contrato_servidor                                                                                             \n";
    $stSql .= "     LEFT JOIN (SELECT especialidade.cod_especialidade                                                                               \n";
    $stSql .= "                     , especialidade.descricao                                                                                       \n";
    $stSql .= "                     , contrato_servidor_especialidade_funcao.cod_contrato                                                           \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_especialidade_funcao                                                                \n";
    $stSql .= "                     , (  SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                 \n";
    $stSql .= "                               , max(timestamp) as timestamp                                                                         \n";
    $stSql .= "                            FROM pessoal.contrato_servidor_especialidade_funcao                                                      \n";
    $stSql .= "                        GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao  \n";
    $stSql .= "                     , pessoal.especialidade                                                                                         \n";
    $stSql .= "                 WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato \n";
    $stSql .= "                   AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp    \n";
    $stSql .= "                   AND contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_funcao \n";
    $stSql .= "            ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                  \n";
    $stSql .= "             , pessoal.contrato                                                                                                      \n";
    $stSql .= "             , pessoal.servidor_contrato_servidor                                                                                    \n";
    $stSql .= "             , pessoal.servidor                                                                                                      \n";
    $stSql .= "             , sw_cgm_pessoa_fisica                                                                                                  \n";
    $stSql .= "             , sw_cgm                                                                                                                \n";
    $stSql .= "             , pessoal.contrato_servidor_funcao                                                                                      \n";
    $stSql .= "             , (  SELECT contrato_servidor_funcao.cod_contrato                                                                       \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                                 \n";
    $stSql .= "                    FROM pessoal.contrato_servidor_funcao                                                                            \n";
    $stSql .= "                GROUP BY contrato_servidor_funcao.cod_contrato) as max_contrato_servidor_funcao                                      \n";
    $stSql .= "             , pessoal.cargo                                                                                                         \n";
    $stSql .= "             , pessoal.contrato_servidor_orgao                                                                                       \n";
    $stSql .= "             , (  SELECT contrato_servidor_orgao.cod_contrato                                                                        \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                                 \n";
    $stSql .= "                    FROM pessoal.contrato_servidor_orgao                                                                             \n";
    $stSql .= "                GROUP BY contrato_servidor_orgao.cod_contrato) as max_contrato_servidor_orgao                                        \n";
    $stSql .= "             , organograma.orgao                                                                                                     \n";
    $stSql .= "             , organograma.vw_orgao_nivel                                                                                            \n";
    $stSql .= "         WHERE evento_calculado.cod_evento                               = registro_evento.cod_evento                                \n";
    $stSql .= "           AND evento_calculado.cod_registro                             = registro_evento.cod_registro                              \n";
    $stSql .= "           AND evento_calculado.timestamp_registro                       = registro_evento.timestamp                                 \n";
    $stSql .= "           AND registro_evento.cod_registro                              = registro_evento_periodo.cod_registro                      \n";
    $stSql .= "           AND registro_evento_periodo.cod_contrato                      = contrato_servidor_periodo.cod_contrato                    \n";
    $stSql .= "           AND registro_evento_periodo.cod_periodo_movimentacao          = contrato_servidor_periodo.cod_periodo_movimentacao        \n";
    $stSql .= "           AND contrato_servidor_periodo.cod_contrato                    = contrato_servidor.cod_contrato                            \n";
    $stSql .= "           AND contrato_servidor.cod_contrato                            = contrato.cod_contrato                                     \n";
    $stSql .= "           AND contrato_servidor.cod_contrato                            = servidor_contrato_servidor.cod_contrato                   \n";
    $stSql .= "           AND servidor_contrato_servidor.cod_servidor                   = servidor.cod_servidor                                     \n";
    $stSql .= "           AND servidor.numcgm                                           = sw_cgm_pessoa_fisica.numcgm                               \n";
    $stSql .= "           AND sw_cgm_pessoa_fisica.numcgm                               = sw_cgm.numcgm                                             \n";
    $stSql .= "           AND contrato_servidor.cod_contrato                            = contrato_servidor_funcao.cod_contrato                     \n";
    $stSql .= "           AND contrato_servidor_funcao.cod_contrato                     = max_contrato_servidor_funcao.cod_contrato                 \n";
    $stSql .= "           AND contrato_servidor_funcao.timestamp                        = max_contrato_servidor_funcao.timestamp                    \n";
    $stSql .= "           AND contrato_servidor_funcao.cod_cargo                        = cargo.cod_cargo                                           \n";
    $stSql .= "           AND contrato_servidor.cod_contrato                            = contrato_servidor_orgao.cod_contrato                      \n";
    $stSql .= "           AND contrato_servidor_orgao.cod_contrato                      = max_contrato_servidor_orgao.cod_contrato                  \n";
    $stSql .= "           AND contrato_servidor_orgao.timestamp                         = max_contrato_servidor_orgao.timestamp                     \n";
    $stSql .= "           AND contrato_servidor_orgao.cod_orgao                         = orgao.cod_orgao                                           \n";
    $stSql .= "           AND orgao.cod_orgao                                           = vw_orgao_nivel.cod_orgao                                  \n";
    $stSql .= "      GROUP BY contrato_servidor_periodo.cod_periodo_movimentacao                                                                    \n";
    $stSql .= "             , registro, registro_evento.cod_registro,evento_calculado.cod_evento,evento_calculado.timestamp_registro                \n";
    $stSql .= "             , evento_calculado.valor                                                                                                \n";
    $stSql .= "             ) as evento_calculado                                                                                         \n";

    return $stSql;
}

function recuperaContratosCalculados(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : " ORDER BY nom_cgm";
    $stSql  = $this->montaRecuperaContratosCalculados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosCalculados()
{

    $stSql  = "SELECT *                                                                                                 
               FROM (SELECT contrato.registro                                                                         
                          , contrato.cod_contrato                                                                     
                          , servidor_contrato_servidor.numcgm                                                         
                          , servidor_contrato_servidor.nom_cgm                                                        
                          , servidor_contrato_servidor.cod_orgao ";                                                      

    if ($this->getDado('boJoinLocal')) {
        $stSql .= "             , servidor_contrato_servidor.cod_local \n";
    }
    if ($this->getDado('boFiltroEvento')) {
        $stSql .= "         , evento_calculado.cod_evento \n";
    }

    $stSql .= "             , registro_evento_periodo.cod_periodo_movimentacao                                          
                      FROM folhapagamento.registro_evento_periodo                           
                         , (SELECT servidor_contrato_servidor.cod_contrato                                           
                                 , sw_cgm.numcgm                                                                     
                                 , sw_cgm.nom_cgm                                                                    
                                 , contrato_servidor_orgao.cod_orgao \n";

    if ($this->getDado('boJoinLocal')) {
        $stSql .= "                     , contrato_servidor_local.cod_local                                             \n";
    }

    $stSql .= "                  FROM pessoal.servidor_contrato_servidor                       \n";

    if ($this->getDado('boJoinLocal')) {
        $stSql .= "            INNER JOIN pessoal.contrato_servidor_local                          
                                ON servidor_contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato    
                        INNER JOIN (  SELECT contrato_servidor_local.cod_contrato                                    
                                            , max(timestamp) as timestamp                                            
                                         FROM pessoal.contrato_servidor_local               
                                     GROUP BY contrato_servidor_local.cod_contrato) as max_contrato_servidor_local   
                                ON contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato   
                               AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp         \n";
    }

    $stSql .= "                     , pessoal.servidor                                         
                         , sw_cgm                                                                            
                         , pessoal.contrato_servidor_orgao                          
                         , (  SELECT contrato_servidor_orgao.cod_contrato                                    
                                    , max(timestamp) as timestamp                                            
                                 FROM pessoal.contrato_servidor_orgao               
                             GROUP BY contrato_servidor_orgao.cod_contrato) as max_contrato_servidor_orgao   
                     WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                   
                       AND servidor.numcgm = sw_cgm.numcgm                                                   
                       AND servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato    
                       AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato   
                       AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp         
                     UNION                                                                                   
                    SELECT contrato_pensionista.cod_contrato                                                 
                         , sw_cgm.numcgm                                                                     
                         , sw_cgm.nom_cgm                                                                    
                         , contrato_pensionista_orgao.cod_orgao                                              \n";

    if ($this->getDado('boJoinLocal')) {
        $stSql .= "                     , contrato_servidor_local.cod_local                                             \n";
    }

    $stSql .= "                  FROM pessoal.contrato_pensionista                             \n";

    if ($this->getDado('boJoinLocal')) {
        $stSql .= "            INNER JOIN pessoal.contrato_servidor_local                          
                            ON contrato_pensionista.cod_contrato_cedente = contrato_servidor_local.cod_contrato  
                    INNER JOIN (  SELECT contrato_servidor_local.cod_contrato                                    
                                        , max(timestamp) as timestamp                                            
                                     FROM pessoal.contrato_servidor_local               
                                 GROUP BY contrato_servidor_local.cod_contrato) as max_contrato_servidor_local   
                            ON contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato   
                           AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp         \n";
    }

    $stSql .= "                     , pessoal.pensionista                                      
                             , sw_cgm                                                                            
                             , pessoal.contrato_pensionista_orgao                       
                             , (  SELECT contrato_pensionista_orgao.cod_contrato                                 
                                        , max(timestamp) as timestamp                                            
                                     FROM pessoal.contrato_pensionista_orgao            
                                 GROUP BY contrato_pensionista_orgao.cod_contrato) as max_contrato_pensionista_orgao
                         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                
                           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente      
                           AND pensionista.numcgm = sw_cgm.numcgm                                                
                           AND contrato_pensionista.cod_contrato = contrato_pensionista_orgao.cod_contrato       
                           AND contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                           AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp   
                           ) as servidor_contrato_servidor                                                       
                     , pessoal.contrato                                                 
                     , folhapagamento.registro_evento                                   
                     , folhapagamento.ultimo_registro_evento                            
                     , folhapagamento.evento_calculado                                  
                 WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro                       
                   AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro                        
                   AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento                            
                   AND registro_evento.timestamp = ultimo_registro_evento.timestamp                              
                   AND registro_evento.cod_registro = evento_calculado.cod_registro                              
                   AND registro_evento.cod_evento = evento_calculado.cod_evento                                  
                   AND registro_evento.timestamp = evento_calculado.timestamp_registro                           
                   AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato            
                   AND registro_evento_periodo.cod_contrato = contrato.cod_contrato                              
                   AND registro_evento_periodo.cod_contrato NOT IN (SELECT cod_contrato                          
                                           FROM pessoal.contrato_servidor_caso_causa )  
              GROUP BY contrato.registro                                                                         
                     , contrato.cod_contrato                                                                     
                     , servidor_contrato_servidor.numcgm                                                         
                     , servidor_contrato_servidor.cod_orgao \n";

    if ($this->getDado('boJoinLocal')) {
        $stSql .= "             , servidor_contrato_servidor.cod_local \n";
    }
    if ($this->getDado('boFiltroEvento')) {
        $stSql .= "         , evento_calculado.cod_evento \n";
    }

    $stSql .= "           , servidor_contrato_servidor.nom_cgm,registro_evento_periodo.cod_periodo_movimentacao) as contratos_calculados ";

    return $stSql;
}

function recuperaRelatorioFichaFinanceira(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : " ORDER BY descricao";
    $stSql  = $this->montaRecuperaRelatorioFichaFinanceira().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioFichaFinanceira()
{
    $stSql  = "   SELECT evento_calculado.*                                                                                                                                    \n";
    $stSql .= "        , getDesdobramentoSalario(evento_calculado.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto                                                                        \n";
    $stSql .= "        , contrato_servidor_periodo.cod_contrato                                                                                                                \n";
    $stSql .= "        , evento.codigo                                                                                                                                         \n";
    $stSql .= "        , trim(evento.descricao) as descricao                                                                                                                   \n";
    $stSql .= "        , evento.natureza                                                                                                                                       \n";
    $stSql .= "        , CASE evento.natureza                                                                                                                                  \n";
    $stSql .= "          WHEN 'P' THEN 'proventos'                                                                                                                             \n";
    $stSql .= "          WHEN 'D' THEN 'descontos'                                                                                                                             \n";
    $stSql .= "          WHEN 'B' THEN 'base'                                                                                                                                  \n";
    $stSql .= "          END as proventos_descontos                                                                                                                            \n";
    $stSql .= "        , sw_cgm.nom_cgm                                                                                                                                        \n";
    $stSql .= "        , contrato.registro                                                                                                                                     \n";
    $stSql .= "        , contrato_servidor.cod_funcao                                                                                                                          \n";
    $stSql .= "        , contrato_servidor.funcao                                                                                                                              \n";
    $stSql .= "        , evento_configuracao_evento.cod_configuracao                                                                                                           \n";
    $stSql .= "        , proporcional                                                                                                                                          \n";
    $stSql .= "     FROM folhapagamento.evento_calculado                                                                                                                       \n";
    $stSql .= "        , folhapagamento.registro_evento                                                                                                                        \n";
    $stSql .= "        , folhapagamento.evento                                                                                                                                 \n";
    $stSql .= "        , folhapagamento.sequencia_calculo_evento                                                                                                               \n";
    $stSql .= "        , folhapagamento.sequencia_calculo                                                                                                                      \n";
    $stSql .= "        , folhapagamento.evento_evento                                                                                                                          \n";
    $stSql .= "        , (  SELECT cod_evento                                                                                                                                  \n";
    $stSql .= "                  , max(timestamp) as timestamp                                                                                                                 \n";
    $stSql .= "               FROM folhapagamento.evento_evento                                                                                                                \n";
    $stSql .= "           GROUP BY cod_evento) as max_evento_evento                                                                                                            \n";
    $stSql .= "        , folhapagamento.evento_configuracao_evento                                                                                                             \n";
    $stSql .= "        , folhapagamento.registro_evento_periodo                                                                                                                \n";
    $stSql .= "        , folhapagamento.contrato_servidor_periodo                                                                                                              \n";
    $stSql .= "        , folhapagamento.periodo_movimentacao                                                                                                                   \n";
    $stSql .= "        , (SELECT contrato_servidor.cod_contrato                                                                                                                \n";
    $stSql .= "                , servidor.cod_servidor                                                                                                                         \n";
    $stSql .= "                , contrato_servidor_funcao.cod_cargo as cod_funcao                                                                                              \n";
    $stSql .= "                , cargo.descricao as funcao                                                                                                                     \n";
    $stSql .= "                , contrato_servidor_orgao.cod_orgao                                                                                                             \n";
    $stSql .= "                , servidor.numcgm                                                                                                                               \n";
    $stSql .= "             FROM pessoal.contrato_servidor                                                                                                                     \n";
    $stSql .= "                , pessoal.servidor_contrato_servidor                                                                                                            \n";
    $stSql .= "                , pessoal.servidor                                                                                                                              \n";
    $stSql .= "                , pessoal.contrato_servidor_funcao                                                                                                              \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                        \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                         \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_funcao                                                                                                    \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                                       \n";
    $stSql .= "                , pessoal.cargo                                                                                                                                 \n";
    $stSql .= "                , pessoal.contrato_servidor_orgao                                                                                                               \n";
    $stSql .= "                , (  SELECT contrato_servidor_orgao.cod_contrato                                                                                                \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                         \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_orgao                                                                                                     \n";
    $stSql .= "                   GROUP BY contrato_servidor_orgao.cod_contrato) as max_contrato_servidor_orgao                                                                \n";
    $stSql .= "            WHERE contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                      \n";
    $stSql .= "              AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                               \n";
    $stSql .= "              AND contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato                                                                        \n";
    $stSql .= "              AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                             \n";
    $stSql .= "              AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp                                                                   \n";
    $stSql .= "              AND contrato_servidor_funcao.cod_cargo = cargo.cod_cargo                                                                                          \n";
    $stSql .= "              AND contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                                                                         \n";
    $stSql .= "              AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                               \n";
    $stSql .= "              AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                                                                     \n";
    $stSql .= "            UNION                                                                                                                                               \n";
    $stSql .= "           SELECT contrato_pensionista.cod_contrato                                                                                                             \n";
    $stSql .= "                , 0 as cod_servidor                                                                                                                             \n";
    $stSql .= "                , 0 as cod_funcao                                                                                                                               \n";
    $stSql .= "                , '' as funcao                                                                                                                                  \n";
    $stSql .= "                , 0 as cod_orgao                                                                                                                                \n";
    $stSql .= "                , pensionista.numcgm                                                                                                                            \n";
    $stSql .= "             FROM pessoal.contrato_pensionista                                                                                                                  \n";
    $stSql .= "                , pessoal.pensionista                                                                                                                           \n";
    $stSql .= "            WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                            \n";
    $stSql .= "              AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as contrato_servidor                                            \n";
    $stSql .= "LEFT JOIN (SELECT especialidade.cod_especialidade                                                                                                               \n";
    $stSql .= "                , especialidade.descricao                                                                                                                       \n";
    $stSql .= "                , contrato_servidor_especialidade_funcao.cod_contrato                                                                                           \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_funcao                                                                                                \n";
    $stSql .= "                , (  SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                         \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_especialidade_funcao                                                                                      \n";
    $stSql .= "                   GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao                                  \n";
    $stSql .= "                , pessoal.especialidade                                                                                                                         \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                                 \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp                                    \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_funcao         \n";
    $stSql .= "       ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                                                  \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_local.*                                                                                                                     \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local                                                                                                               \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                        \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                         \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                                                                                                     \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                                                                                        \n";
    $stSql .= "            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                                               \n";
    $stSql .= "              AND contrato_servidor_local.timestamp    = max_contrato_servidor_local.timestamp) as contrato_servidor_local                                      \n";
    $stSql .= "              ON contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato                                                                          \n";
    $stSql .= "        , pessoal.contrato                                                                                                                                      \n";
    $stSql .= "        , sw_cgm_pessoa_fisica                                                                                                                                  \n";
    $stSql .= "        , sw_cgm                                                                                                                                                \n";
    $stSql .= "    WHERE evento_calculado.cod_evento                               = registro_evento.cod_evento                                                                \n";
    $stSql .= "      AND evento_calculado.cod_registro                             = registro_evento.cod_registro                                                              \n";
    $stSql .= "      AND evento_calculado.timestamp_registro                       = registro_evento.timestamp                                                                 \n";
    $stSql .= "      AND registro_evento.cod_evento                                = evento.cod_evento                                                                         \n";
    $stSql .= "      AND registro_evento.cod_registro                              = registro_evento_periodo.cod_registro                                                      \n";
    $stSql .= "      AND registro_evento_periodo.cod_contrato                      = contrato_servidor_periodo.cod_contrato                                                    \n";
    $stSql .= "      AND registro_evento_periodo.cod_periodo_movimentacao          = contrato_servidor_periodo.cod_periodo_movimentacao                                        \n";
    $stSql .= "      AND contrato_servidor_periodo.cod_contrato                    = contrato_servidor.cod_contrato                                                            \n";
    $stSql .= "      AND contrato_servidor.numcgm                                  = sw_cgm_pessoa_fisica.numcgm                                                               \n";
    $stSql .= "      AND sw_cgm_pessoa_fisica.numcgm                               = sw_cgm.numcgm                                                                             \n";
    $stSql .= "      AND contrato_servidor.cod_contrato                            = contrato.cod_contrato                                                                     \n";
    $stSql .= "      AND evento.cod_evento                                         = evento_evento.cod_evento                                                                  \n";
    $stSql .= "      AND evento_evento.cod_evento                                  = max_evento_evento.cod_evento                                                              \n";
    $stSql .= "      AND evento_evento.timestamp                                   = max_evento_evento.timestamp                                                               \n";
    $stSql .= "      AND evento_evento.cod_evento                                  = evento_configuracao_evento.cod_evento                                                     \n";
    $stSql .= "      AND evento_evento.timestamp                                   = evento_configuracao_evento.timestamp                                                      \n";
    $stSql .= "      AND evento.cod_evento                                         = sequencia_calculo_evento.cod_evento                                                       \n";
    $stSql .= "      AND sequencia_calculo_evento.cod_sequencia                    = sequencia_calculo.cod_sequencia                                                           \n";
    $stSql .= "      AND contrato_servidor_periodo.cod_periodo_movimentacao        = periodo_movimentacao.cod_periodo_movimentacao                                             \n";

   return $stSql;
}

function recuperaEventosCalculados(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY descricao ";
    $stSql  = $this->montaRecuperaEventosCalculados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosCalculados()
{
    $stSql  = "SELECT registro_evento_parcela.parcela as quantidade_parc                                      \n";
    $stSql .= "     , evento_calculado.valor                                                                   \n";
    $stSql .= "     , evento_calculado.quantidade                                                              \n";
    $stSql .= "     , evento_calculado.cod_registro                                                            \n";
    $stSql .= "     , ( CASE WHEN evento_calculado.desdobramento IS NOT NULL                                   \n";
    $stSql .= "                 THEN evento.descricao ||' '|| getDesdobramentoSalario(evento_calculado.desdobramento,'".Sessao::getEntidade()."') \n";
    $stSql .= "                 ELSE evento.descricao                                                          \n";
    $stSql .= "       END ) as descricao                                                                       \n";
    $stSql .= "     , evento.descricao as nom_evento                                                           \n";
    $stSql .= "     , evento.cod_evento                                                                        \n";
    $stSql .= "     , evento.codigo                                                                            \n";
    $stSql .= "     , evento.natureza                                                                          \n";
    $stSql .= "     , evento.apresentar_contracheque                                                           \n";
    $stSql .= "     , evento_calculado.desdobramento                                                           \n";
    $stSql .= "     , getDesdobramentoSalario(evento_calculado.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto           \n";
    $stSql .= "  FROM folhapagamento.ultimo_registro_evento                                                    \n";
    $stSql .= "   INNER JOIN folhapagamento.registro_evento                                                    \n";
    $stSql .= "         ON ultimo_registro_evento.cod_evento = registro_evento.cod_evento                      \n";
    $stSql .= "        AND ultimo_registro_evento.cod_registro = registro_evento.cod_registro                  \n";
    $stSql .= "        AND ultimo_registro_evento.timestamp = registro_evento.timestamp                        \n";
    $stSql .= " INNER JOIN folhapagamento.registro_evento_periodo                                              \n";
    $stSql .= "         ON registro_evento.cod_registro = registro_evento_periodo.cod_registro                 \n";
    $stSql .= " INNER JOIN folhapagamento.evento_calculado                                                     \n";
    $stSql .= "         ON ultimo_registro_evento.cod_evento = evento_calculado.cod_evento                     \n";
    $stSql .= "        AND ultimo_registro_evento.cod_registro = evento_calculado.cod_registro                 \n";
    $stSql .= "        AND ultimo_registro_evento.timestamp = evento_calculado.timestamp_registro              \n";
    $stSql .= "  LEFT JOIN folhapagamento.registro_evento_parcela                                              \n";
    $stSql .= "         ON ultimo_registro_evento.cod_evento = registro_evento_parcela.cod_evento              \n";
    $stSql .= "        AND ultimo_registro_evento.cod_registro = registro_evento_parcela.cod_registro          \n";
    $stSql .= "        AND ultimo_registro_evento.timestamp = registro_evento_parcela.timestamp                \n";
    $stSql .= " INNER JOIN folhapagamento.evento                                                               \n";
    $stSql .= "         ON evento_calculado.cod_evento = evento.cod_evento                                     \n";
    $stSql .= " WHERE 1=1                                                                                      \n";

    return $stSql;
}

function recuperaEventosCalculadosDoServidor(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY numcgm ";
    $stSql  = $this->montaRecuperaEventosCalculadosDoServidor().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosCalculadosDoServidor()
{
    $stSql  = "SELECT evento_calculado.*                                                               \n";
    $stSql .= "     , evento.descricao                                                                 \n";
    $stSql .= "     , evento.codigo                                                                    \n";
    $stSql .= "  FROM folhapagamento.registro_evento_periodo                  \n";
    $stSql .= "     , folhapagamento.registro_evento                          \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento                   \n";
    $stSql .= "     , folhapagamento.evento_calculado                         \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                      \n";
    $stSql .= "     , pessoal.servidor                                        \n";
    $stSql .= "     , folhapagamento.evento                                   \n";
    $stSql .= " WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro              \n";
    $stSql .= "   AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro               \n";
    $stSql .= "   AND registro_evento.cod_evento   = ultimo_registro_evento.cod_evento                 \n";
    $stSql .= "   AND registro_evento.timestamp    = ultimo_registro_evento.timestamp                  \n";
    $stSql .= "   AND ultimo_registro_evento.cod_registro = evento_calculado.cod_registro              \n";
    $stSql .= "   AND ultimo_registro_evento.cod_evento   = evento_calculado.cod_evento                \n";
    $stSql .= "   AND ultimo_registro_evento.timestamp    = evento_calculado.timestamp_registro        \n";
    $stSql .= "   AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato   \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                  \n";
    $stSql .= "   AND evento_calculado.cod_evento = evento.cod_evento                                  \n";

    return $stSql;
}

function recuperaEventosCalculadosFichaFinanceira(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
//     $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY descricao ";
    $stSql  = $this->montaRecuperaEventosCalculadosFichaFinanceira();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosCalculadosFichaFinanceira()
{
    $stSql  = "    SELECT evento_calculado.*                                                                       \n";
    $stSql .= "         , CASE WHEN evento_calculado.apresenta_parcela = TRUE THEN (evento_calculado.quantidade::INTEGER)::VARCHAR ";
    $stSql .= "	               ELSE REPLACE((evento_calculado.quantidade::NUMERIC)::VARCHAR, '.', ',') END AS quantidade_parcelas  ";
    $stSql .= "      FROM recuperarEventosCalculados(".$this->getDado("cod_configuracao").",".$this->getDado("cod_periodo_movimentacao").",".$this->getDado("cod_contrato").",".$this->getDado("cod_complementar").",'".Sessao::getEntidade()."','".$this->getDado("ordem")."') as evento_calculado \n";

//     $stSql .= "SELECT evento_calculado.*                                                                       \n";
//     $stSql .= "     , evento.descricao                                                                         \n";
//     $stSql .= "     , evento.codigo                                                                            \n";
//     $stSql .= "     , evento.natureza                                                                          \n";
//     $stSql .= "     , sequencia_calculo.sequencia                                                              \n";
//     $stSql .= "     , getDesdobramentoFolha(1,evento_calculado.desdobramento) as desdobramento_texto     \n";
//     $stSql .= "  FROM folhapagamento.registro_evento_periodo                          \n";
//     $stSql .= "     , folhapagamento.registro_evento                                  \n";
//     $stSql .= "     , folhapagamento.ultimo_registro_evento                           \n";
//     $stSql .= "     , folhapagamento.evento_calculado                                 \n";
//     $stSql .= "     , folhapagamento.evento                                           \n";
//     $stSql .= "     , folhapagamento.sequencia_calculo_evento                         \n";
//     $stSql .= "     , folhapagamento.sequencia_calculo                                \n";
//     $stSql .= " WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro                      \n";
//     $stSql .= "   AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro                       \n";
//     $stSql .= "   AND registro_evento.cod_evento   = ultimo_registro_evento.cod_evento                         \n";
//     $stSql .= "   AND registro_evento.timestamp    = ultimo_registro_evento.timestamp                          \n";
//     $stSql .= "   AND ultimo_registro_evento.cod_registro = evento_calculado.cod_registro                      \n";
//     $stSql .= "   AND ultimo_registro_evento.cod_evento   = evento_calculado.cod_evento                        \n";
//     $stSql .= "   AND ultimo_registro_evento.timestamp    = evento_calculado.timestamp_registro                \n";
//     $stSql .= "   AND evento_calculado.cod_evento = evento.cod_evento                                          \n";
//     $stSql .= "   AND evento_calculado.cod_evento = sequencia_calculo_evento.cod_evento                        \n";
//     $stSql .= "   AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia                 \n";
    return $stSql;
}

function recuperaEventosCalculadosFiltroFicha(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY cod_evento ";
    $stSql  = $this->montaRecuperaEventosCalculadosFiltroFicha().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosCalculadosFiltroFicha()
{
    $stSql  = "SELECT evento_calculado.cod_evento                                                              \n";
    $stSql .= "  FROM folhapagamento.registro_evento_periodo                                                   \n";
    $stSql .= "     , folhapagamento.registro_evento                                                           \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento                                                    \n";
    $stSql .= "     , folhapagamento.evento_calculado                                                          \n";
    $stSql .= " WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro                      \n";
    $stSql .= "   AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro                       \n";
    $stSql .= "   AND registro_evento.cod_evento   = ultimo_registro_evento.cod_evento                         \n";
    $stSql .= "   AND registro_evento.timestamp    = ultimo_registro_evento.timestamp                          \n";
    $stSql .= "   AND ultimo_registro_evento.cod_registro = evento_calculado.cod_registro                      \n";
    $stSql .= "   AND ultimo_registro_evento.cod_evento   = evento_calculado.cod_evento                        \n";
    $stSql .= "   AND ultimo_registro_evento.timestamp    = evento_calculado.timestamp_registro                \n";

    return $stSql;
}

function recuperaEventosCalculadosParaExclusao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaEventosCalculadosParaExclusao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosCalculadosParaExclusao()
{
    $stSql  = "SELECT evento_calculado.*\n";
    $stSql .= "  FROM folhapagamento.registro_evento_periodo\n";
    $stSql .= "     , folhapagamento.registro_evento\n";
    $stSql .= "     , folhapagamento.evento_calculado\n";
    $stSql .= " WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro\n";
    $stSql .= "   AND registro_evento.cod_registro = evento_calculado.cod_registro\n";
    $stSql .= "   AND registro_evento.cod_evento   = evento_calculado.cod_evento\n";
    $stSql .= "   AND registro_evento.timestamp    = evento_calculado.timestamp_registro\n";

    return $stSql;
}

function recuperaRelatorioFolhaAnalitica(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY contrato.cod_contrato ";
    $stSql  = $this->montaRecuperaRelatorioFolhaAnalitica().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioFolhaAnalitica()
{
    $stSql  = "   SELECT registro                                                                                                                                                  \n";
    $stSql .= "        , sw_cgm.numcgm                                                                                                                                             \n";
    $stSql .= "        , sw_cgm.nom_cgm                                                                                                                                            \n";
    $stSql .= "        , cod_orgao                                                                                                                                                 \n";
    $stSql .= "        , descricao_lotacao                                                                                                                                         \n";
    $stSql .= "        , cod_local                                                                                                                                                 \n";
    $stSql .= "        , descricao_local                                                                                                                                           \n";
    $stSql .= "        , horas_mensais                                                                                                                                             \n";
    $stSql .= "        , regime.cod_regime as cod_regime_cargo                                                                                                                     \n";
    $stSql .= "        , regime.descricao as descricao_regime_cargo                                                                                                                \n";
    $stSql .= "        , sub_divisao.cod_sub_divisao as cod_sub_divisao_cargo                                                                                                      \n";
    $stSql .= "        , sub_divisao.descricao as descricao_sub_divisao_cargo                                                                                                      \n";
    $stSql .= "        , cargo.cod_cargo                                                                                                                                           \n";
    $stSql .= "        , cargo.descricao as descricao_cargo                                                                                                                        \n";
    $stSql .= "        , especialidade.cod_especialidade as cod_especialidade_cargo                                                                                                \n";
    $stSql .= "        , especialidade.descricao as descricao_especialidade_cargo                                                                                                  \n";
    $stSql .= "        , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato,0,'".Sessao::getEntidade()."') as situacao \n";

    $stSql .= "        , contrato_servidor_regime_funcao.descricao_regime_funcao                                                                                                   \n";
    $stSql .= "        , contrato_servidor_regime_funcao.cod_regime_funcao                                                                                                         \n";
    $stSql .= "        , contrato_servidor_sub_divisao_funcao.descricao_sub_divisao_funcao                                                                                         \n";
    $stSql .= "        , contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao                                                                                               \n";
    $stSql .= "        , contrato_servidor_funcao.descricao_funcao                                                                                                                 \n";
    $stSql .= "        , contrato_servidor_funcao.cod_funcao                                                                                                                       \n";
    $stSql .= "        , especialidade_funcao.descricao_especialidade_funcao                                                                                                       \n";
    $stSql .= "        , especialidade_funcao.cod_especialidade_funcao                                                                                                             \n";
    $stSql .= "        , contrato_servidor_previdencia.descricao_previdencia                                                                                                       \n";
    $stSql .= "        , contrato_servidor_padrao.descricao_padrao                                                                                                                 \n";
    $stSql .= "        , to_char(contrato_servidor_nomeacao_posse.dt_posse,'dd/mm/yyyy') as dt_posse                                                                               \n";
    $stSql .= "        , to_char(contrato_servidor_nomeacao_posse.dt_nomeacao,'dd/mm/yyyy') as dt_nomeacao                                                                         \n";
    $stSql .= "        , to_char(contrato_servidor_nomeacao_posse.dt_admissao,'dd/mm/yyyy') as dt_admissao                                                                         \n";
    $stSql .= "        , CASE WHEN contratos.contratos > 1 THEN 'Sim'                                                                                                              \n";
    $stSql .= "          ELSE 'Não'                                                                                                                                                \n";
    $stSql .= "           END as multiplos                                                                                                                                         \n";
    $stSql .= "        , descricao_nivel_padrao                                                                                                                                    \n";
    $stSql .= "     FROM (SELECT servidor_contrato_servidor.cod_contrato                                                                                                           \n";
    $stSql .= "                , servidor.numcgm                                                                                                                                   \n";
    $stSql .= "                , contrato_servidor.cod_regime                                                                                                                      \n";
    $stSql .= "                , contrato_servidor.cod_cargo                                                                                                                       \n";
    $stSql .= "                , contrato_servidor.cod_sub_divisao                                                                                                                 \n";
    $stSql .= "                , contrato_servidor.ativo                                                                                                                           \n";
    $stSql .= "             FROM pessoal.servidor_contrato_servidor                                                                                                                \n";
    $stSql .= "                , pessoal.servidor                                                                                                                                  \n";
    $stSql .= "                , pessoal.contrato_servidor                                                                                                                         \n";
    $stSql .= "            WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                   \n";
    $stSql .= "              AND servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato                                                                          \n";
    $stSql .= "            UNION                                                                                                                                                   \n";
    $stSql .= "           SELECT contrato_pensionista.cod_contrato                                                                                                                 \n";
    $stSql .= "                , pensionista.numcgm                                                                                                                                \n";
    $stSql .= "                , 0 as cod_regime                                                                                                                                   \n";
    $stSql .= "                , 0 as cod_cargo                                                                                                                                    \n";
    $stSql .= "                , 0 as cod_sub_divisao                                                                                                                              \n";
    $stSql .= "                , false as ativo                                                                                                                                    \n";
    $stSql .= "             FROM pessoal.contrato_pensionista                                                                                                                      \n";
    $stSql .= "                , pessoal.pensionista                                                                                                                               \n";
    $stSql .= "            WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                                \n";
    $stSql .= "              AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor                                                         \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_nivel_padrao.cod_contrato                                                                                                       \n";
    $stSql .= "                , nivel_padrao_nivel.descricao as descricao_nivel_padrao                                                                                            \n";
    $stSql .= "             FROM pessoal.contrato_servidor_nivel_padrao                                                                                                            \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_nivel_padrao                                                                                                  \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_nivel_padrao                                                                                     \n";
    $stSql .= "                , folhapagamento.nivel_padrao                                                                                                                       \n";
    $stSql .= "                , folhapagamento.nivel_padrao_nivel                                                                                                                 \n";
    $stSql .= "                , (  SELECT cod_nivel_padrao                                                                                                                        \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM folhapagamento.nivel_padrao_nivel                                                                                                       \n";
    $stSql .= "                   GROUP BY cod_nivel_padrao) as max_nivel_padrao_nivel                                                                                             \n";
    $stSql .= "            WHERE contrato_servidor_nivel_padrao.cod_contrato = max_contrato_servidor_nivel_padrao.cod_contrato                                                     \n";
    $stSql .= "              AND contrato_servidor_nivel_padrao.timestamp    = max_contrato_servidor_nivel_padrao.timestamp                                                        \n";
    $stSql .= "              AND contrato_servidor_nivel_padrao.cod_nivel_padrao = nivel_padrao.cod_nivel_padrao                                                                   \n";
    $stSql .= "              AND nivel_padrao.cod_nivel_padrao = nivel_padrao_nivel.cod_nivel_padrao                                                                               \n";
    $stSql .= "              AND nivel_padrao_nivel.cod_nivel_padrao = max_nivel_padrao_nivel.cod_nivel_padrao                                                                     \n";
    $stSql .= "              AND nivel_padrao_nivel.timestamp        = max_nivel_padrao_nivel.timestamp) as contrato_servidor_nivel_padrao                                         \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor_nivel_padrao.cod_contrato                                                                                       \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_nomeacao_posse.cod_contrato                                                                                                     \n";
    $stSql .= "                , contrato_servidor_nomeacao_posse.dt_posse                                                                                                         \n";
    $stSql .= "                , contrato_servidor_nomeacao_posse.dt_nomeacao                                                                                                      \n";
    $stSql .= "                , contrato_servidor_nomeacao_posse.dt_admissao                                                                                                      \n";
    $stSql .= "             FROM pessoal.contrato_servidor_nomeacao_posse                                                                                                          \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_nomeacao_posse                                                                                                \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse                                                                                   \n";
    $stSql .= "            WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato                                                 \n";
    $stSql .= "              AND contrato_servidor_nomeacao_posse.timestamp    = max_contrato_servidor_nomeacao_posse.timestamp) as contrato_servidor_nomeacao_posse               \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato                                                                                     \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_padrao.cod_contrato                                                                                                             \n";
    $stSql .= "                , padrao.descricao as descricao_padrao                                                                                                              \n";
    $stSql .= "             FROM pessoal.contrato_servidor_padrao                                                                                                                  \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_padrao                                                                                                        \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_padrao                                                                                           \n";
    $stSql .= "                , folhapagamento.padrao                                                                                                                             \n";
    $stSql .= "            WHERE contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato                                                                 \n";
    $stSql .= "              AND contrato_servidor_padrao.timestamp    = max_contrato_servidor_padrao.timestamp                                                                    \n";
    $stSql .= "              AND contrato_servidor_padrao.cod_padrao   = padrao.cod_padrao) as contrato_servidor_padrao                                                            \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor_padrao.cod_contrato                                                                                             \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_previdencia.cod_contrato                                                                                                        \n";
    $stSql .= "                , previdencia_previdencia.descricao as descricao_previdencia                                                                                        \n";
    $stSql .= "             FROM pessoal.contrato_servidor_previdencia                                                                                                             \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_previdencia                                                                                                   \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                                                                      \n";
    $stSql .= "                , folhapagamento.previdencia                                                                                                                        \n";
    $stSql .= "                , folhapagamento.previdencia_previdencia                                                                                                            \n";
    $stSql .= "                , (  SELECT cod_previdencia                                                                                                                         \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM folhapagamento.previdencia_previdencia                                                                                                  \n";
    $stSql .= "                   GROUP BY cod_previdencia) as max_previdencia_previdencia                                                                                         \n";
    $stSql .= "            WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato                                                       \n";
    $stSql .= "              AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp                                                          \n";
    $stSql .= "              AND contrato_servidor_previdencia.cod_previdencia   = previdencia.cod_previdencia                                                                     \n";
    $stSql .= "              AND previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                                                                             \n";
    $stSql .= "              AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                                                             \n";
    $stSql .= "              AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                                                                         \n";
    $stSql .= "              AND previdencia_previdencia.tipo_previdencia = '||quote_literal('o')||') as contrato_servidor_previdencia                                                                  \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor_previdencia.cod_contrato                                                                                        \n";
    $stSql .= "LEFT JOIN pessoal.regime                                                                                                                                            \n";
    $stSql .= "       ON regime.cod_regime = servidor.cod_regime                                                                                                                   \n";
    $stSql .= "LEFT JOIN pessoal.sub_divisao                                                                                                                                       \n";
    $stSql .= "       ON sub_divisao.cod_sub_divisao = servidor.cod_sub_divisao                                                                                                    \n";
    $stSql .= "LEFT JOIN pessoal.cargo                                                                                                                                             \n";
    $stSql .= "       ON cargo.cod_cargo = servidor.cod_cargo                                                                                                                      \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_especialidade_cargo.cod_contrato                                                                                                \n";
    $stSql .= "                , especialidade.descricao                                                                                                                           \n";
    $stSql .= "                , especialidade.cod_especialidade                                                                                                                   \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_cargo                                                                                                     \n";
    $stSql .= "                , pessoal.especialidade                                                                                                                             \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_cargo.cod_especialidade = especialidade.cod_especialidade) as especialidade                                       \n";
    $stSql .= "       ON servidor.cod_contrato = especialidade.cod_contrato                                                                                                        \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_regime_funcao.cod_contrato                                                                                                      \n";
    $stSql .= "                , regime.descricao as descricao_regime_funcao                                                                                                       \n";
    $stSql .= "                , regime.cod_regime as cod_regime_funcao                                                                                                            \n";
    $stSql .= "             FROM pessoal.contrato_servidor_regime_funcao                                                                                                           \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_regime_funcao                                                                                                 \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao                                                                                    \n";
    $stSql .= "                , pessoal.regime                                                                                                                                    \n";
    $stSql .= "            WHERE contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato                                                   \n";
    $stSql .= "              AND contrato_servidor_regime_funcao.timestamp    = max_contrato_servidor_regime_funcao.timestamp                                                      \n";
    $stSql .= "              AND contrato_servidor_regime_funcao.cod_regime   = regime.cod_regime) as contrato_servidor_regime_funcao                                              \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor_regime_funcao.cod_contrato                                                                                      \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_sub_divisao_funcao.cod_contrato                                                                                                 \n";
    $stSql .= "                , sub_divisao.descricao as descricao_sub_divisao_funcao                                                                                             \n";
    $stSql .= "                , sub_divisao.cod_sub_divisao as cod_sub_divisao_funcao                                                                                             \n";
    $stSql .= "             FROM pessoal.contrato_servidor_sub_divisao_funcao                                                                                                      \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_sub_divisao_funcao                                                                                            \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao                                                                               \n";
    $stSql .= "                , pessoal.sub_divisao                                                                                                                               \n";
    $stSql .= "            WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato                                         \n";
    $stSql .= "              AND contrato_servidor_sub_divisao_funcao.timestamp    = max_contrato_servidor_sub_divisao_funcao.timestamp                                            \n";
    $stSql .= "              AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao   = sub_divisao.cod_sub_divisao) as contrato_servidor_sub_divisao_funcao                     \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato                                                                                 \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_funcao.cod_contrato                                                                                                             \n";
    $stSql .= "                , cargo.descricao as descricao_funcao                                                                                                               \n";
    $stSql .= "                , cargo.cod_cargo as cod_funcao                                                                                                                     \n";
    $stSql .= "             FROM pessoal.contrato_servidor_funcao                                                                                                                  \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_funcao                                                                                                        \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                                           \n";
    $stSql .= "                , pessoal.cargo                                                                                                                                     \n";
    $stSql .= "            WHERE contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                                 \n";
    $stSql .= "              AND contrato_servidor_funcao.timestamp    = max_contrato_servidor_funcao.timestamp                                                                    \n";
    $stSql .= "              AND contrato_servidor_funcao.cod_cargo    = cargo.cod_cargo) as contrato_servidor_funcao                                                              \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor_funcao.cod_contrato                                                                                             \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                                                               \n";
    $stSql .= "                , especialidade.descricao as descricao_especialidade_funcao                                                                                         \n";
    $stSql .= "                , especialidade.cod_especialidade as cod_especialidade_funcao                                                                                       \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_funcao                                                                                                    \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_especialidade_funcao                                                                                          \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao                                                                             \n";
    $stSql .= "                , pessoal.especialidade                                                                                                                             \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade                                                        \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                                     \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp) as especialidade_funcao               \n";
    $stSql .= "       ON servidor.cod_contrato = especialidade_funcao.cod_contrato                                                                                                 \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_salario.cod_contrato                                                                                                            \n";
    $stSql .= "                , contrato_servidor_salario.horas_mensais                                                                                                           \n";
    $stSql .= "             FROM pessoal.contrato_servidor_salario                                                                                                                 \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_salario                                                                                                       \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_salario                                                                                          \n";
    $stSql .= "            WHERE contrato_servidor_salario.cod_contrato = max_contrato_servidor_salario.cod_contrato                                                               \n";
    $stSql .= "              AND contrato_servidor_salario.timestamp    = max_contrato_servidor_salario.timestamp) as contrato_servidor_salario                                    \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor_salario.cod_contrato                                                                                            \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_orgao.cod_contrato                                                                                                              \n";
    $stSql .= "                , contrato_servidor_orgao.cod_orgao                                                                                                                 \n";
    $stSql .= "                , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao                                                                                                              \n";
    $stSql .= "             FROM pessoal.contrato_servidor_orgao                                                                                                                   \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_orgao                                                                                                         \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                                            \n";
    $stSql .= "                , organograma.orgao                                                                                                                                 \n";
    $stSql .= "            WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                                   \n";
    $stSql .= "              AND contrato_servidor_orgao.timestamp    = max_contrato_servidor_orgao.timestamp                                                                      \n";
    $stSql .= "              AND contrato_servidor_orgao.cod_orgao    = orgao.cod_orgao ) as contrato_servidor_orgao                                                               \n";
    $stSql .= "       ON servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                                                                                              \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_local.cod_local                                                                                                                 \n";
    $stSql .= "                , contrato_servidor_local.cod_contrato                                                                                                              \n";
    $stSql .= "                , local.descricao as descricao_local                                                                                                                \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local                                                                                                                   \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                            \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                             \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                                                                                                         \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                                                                                            \n";
    $stSql .= "                , organograma.local                                                                                                                                 \n";
    $stSql .= "            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                                                   \n";
    $stSql .= "              AND contrato_servidor_local.timestamp    = max_contrato_servidor_local.timestamp                                                                      \n";
    $stSql .= "              AND contrato_servidor_local.cod_local    = local.cod_local) as contrato_servidor_local                                                                \n";
    $stSql .= "              ON servidor.cod_contrato = contrato_servidor_local.cod_contrato                                                                                       \n";
    $stSql .= "        , pessoal.contrato                                                                                                                                          \n";
    $stSql .= "        , sw_cgm_pessoa_fisica                                                                                                                                      \n";
    $stSql .= "        , sw_cgm                                                                                                                                                    \n";
    $stSql .= "LEFT JOIN (  SELECT numcgm                                                                                                                                          \n";
    $stSql .= "                  , count(cod_contrato) as contratos                                                                                                                \n";
    $stSql .= "               FROM pessoal.servidor_contrato_servidor                                                                                                              \n";
    $stSql .= "                  , pessoal.servidor                                                                                                                                \n";
    $stSql .= "              WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                 \n";
    $stSql .= "           GROUP BY numcgm) as contratos                                                                                                                            \n";
    $stSql .= "       ON sw_cgm.numcgm = contratos.numcgm                                                                                                                          \n";
    $stSql .= "    WHERE servidor.cod_contrato = contrato.cod_contrato                                                                                                             \n";
    $stSql .= "      AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                                                             \n";
    $stSql .= "      AND sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm                                                                                                               \n";

    return $stSql;
}

function recuperaRelatorioFolhaAnaliticaEventoPrevidencia(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY contrato_servidor_previdencia.cod_contrato ";
    $stSql  = $this->montaRecuperaRelatorioFolhaAnaliticaEventoPrevidencia().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaRelatorioFolhaAnaliticaDadosContrato(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY contrato.cod_contrato ";
    $stSql  = $this->montaRecuperaRelatorioFolhaAnalitica(DadosContrato).$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioFolhaAnaliticaDadosContrato()
{
    $stSql  = "   SELECT registro                                                                                                                              \n";
    $stSql .= "        , sw_cgm.numcgm                                                                                                                         \n";
    $stSql .= "        , sw_cgm.nom_cgm                                                                                                                        \n";
    $stSql .= "        , (SELECT descricao FROM pessoal.regime WHERE cod_regime = contrato_servidor_regime_funcao.cod_regime) as descricao_regime_funcao       \n";
    $stSql .= "        , (SELECT descricao FROM pessoal.cargo  WHERE cod_cargo = contrato_servidor_funcao.cod_cargo) as descricao_funcao                       \n";
    $stSql .= "        , (SELECT descricao FROM folhapagamento.padrao WHERE cod_padrao = contrato_servidor_padrao.cod_padrao) as descricao_padrao              \n";
    $stSql .= "     FROM pessoal.contrato                                                                                                                      \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_regime_funcao.*                                                                                             \n";
    $stSql .= "             FROM pessoal.contrato_servidor_regime_funcao                                                                                       \n";
    $stSql .= "                , (SELECT cod_contrato                                                                                                          \n";
    $stSql .= "                        , max(timestamp) as timestamp                                                                                           \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_regime_funcao                                                                               \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao                                                                \n";
    $stSql .= "            WHERE contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato                               \n";
    $stSql .= "              AND contrato_servidor_regime_funcao.timestamp = max_contrato_servidor_regime_funcao.timestamp) as contrato_servidor_regime_funcao \n";
    $stSql .= "       ON contrato.cod_contrato = contrato_servidor_regime_funcao.cod_contrato                                                                  \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_funcao.*                                                                                                    \n";
    $stSql .= "             FROM pessoal.contrato_servidor_funcao                                                                                              \n";
    $stSql .= "                , (SELECT cod_contrato                                                                                                          \n";
    $stSql .= "                        , max(timestamp) as timestamp                                                                                           \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_funcao                                                                                      \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                       \n";
    $stSql .= "            WHERE contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                             \n";
    $stSql .= "              AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp) as contrato_servidor_funcao                      \n";
    $stSql .= "       ON contrato.cod_contrato = contrato_servidor_funcao.cod_contrato                                                                         \n";
    $stSql .= "        , pessoal.servidor_contrato_servidor                                                                                                    \n";
    $stSql .= "        , pessoal.servidor                                                                                                                      \n";
    $stSql .= "        , pessoal.contrato_servidor_padrao                                                                                                      \n";
    $stSql .= "        , (SELECT cod_contrato                                                                                                                  \n";
    $stSql .= "                , max(timestamp) as timestamp                                                                                                   \n";
    $stSql .= "             FROM pessoal.contrato_servidor_padrao                                                                                              \n";
    $stSql .= "           GROUP BY cod_contrato) as max_contrato_servidor_padrao                                                                               \n";
    $stSql .= "        , sw_cgm                                                                                                                                \n";
    $stSql .= "    WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                       \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                       \n";
    $stSql .= "      AND servidor.numcgm = sw_cgm.numcgm                                                                                                       \n";
    $stSql .= "      AND contrato.cod_contrato = contrato_servidor_padrao.cod_contrato                                                                         \n";
    $stSql .= "      AND contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato                                                     \n";
    $stSql .= "      AND contrato_servidor_padrao.timestamp = max_contrato_servidor_padrao.timestamp                                                           \n";

    return $stSql;
}

function montaRecuperaRelatorioFolhaAnaliticaEventoPrevidencia()
{
    $stSql  = " SELECT previdencia_evento.*                                                                            \n";
    $stSql .= "   FROM folhapagamento.previdencia_evento                                                               \n";
    $stSql .= "      , folhapagamento.previdencia_previdencia                                                          \n";
    $stSql .= "      , (  SELECT cod_previdencia                                                                       \n";
    $stSql .= "                , max(timestamp) as timestamp                                                           \n";
    $stSql .= "             FROM folhapagamento.previdencia_previdencia                                                \n";
    $stSql .= "         GROUP BY cod_previdencia) as max_previdencia_previdencia                                       \n";
    $stSql .= "      , folhapagamento.previdencia                                                                      \n";
    $stSql .= "      , pessoal.contrato_servidor_previdencia                                                           \n";
    $stSql .= "      , (  SELECT cod_contrato                                                                          \n";
    $stSql .= "                , max(timestamp) as timestamp                                                           \n";
    $stSql .= "             FROM pessoal.contrato_servidor_previdencia                                                 \n";
    $stSql .= "         GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                    \n";
    $stSql .= "  WHERE previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia                    \n";
    $stSql .= "    AND previdencia_evento.timestamp = previdencia_previdencia.timestamp                                \n";
    $stSql .= "    AND previdencia_evento.cod_tipo = 2                                                                 \n";
    $stSql .= "    AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia           \n";
    $stSql .= "    AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                       \n";
    $stSql .= "    AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia                           \n";
    $stSql .= "    AND previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia                     \n";
    $stSql .= "    AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato     \n";
    $stSql .= "    AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp           \n";

    return $stSql;
}

function recuperaRelatorioFolhaAnaliticaEventoIRRF(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY cod_evento ";
    $stSql  = $this->montaRecuperaRelatorioFolhaAnaliticaEventoIRRF().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioFolhaAnaliticaEventoIRRF()
{
    $stSql  = "SELECT cod_evento                                               \n";
    $stSql .= " FROM folhapagamento.tabela_irrf_evento                         \n";
    $stSql .= "    , folhapagamento.tabela_irrf                                \n";
    $stSql .= "    , (SELECT cod_tabela                                        \n";
    $stSql .= "            , max(timestamp) as timestamp                       \n";
    $stSql .= "         FROM folhapagamento.tabela_irrf                        \n";
    $stSql .= "       GROUP BY cod_tabela) as max_tabela_irrf                  \n";
    $stSql .= "WHERE tabela_irrf_evento.cod_tabela = tabela_irrf.cod_tabela    \n";
    $stSql .= "  AND tabela_irrf_evento.timestamp  = tabela_irrf.timestamp     \n";
    $stSql .= "  AND tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela       \n";
    $stSql .= "  AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp        \n";

    return $stSql;
}

function recuperaRelatorioFolhaAnaliticaEventoFGTS(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY cod_evento ";
    $stSql  = $this->montaRecuperaRelatorioFolhaAnaliticaEventoFGTS().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioFolhaAnaliticaEventoFGTS()
{
    $stSql  = "SELECT cod_evento                                               \n";
    $stSql .= " FROM folhapagamento.fgts_evento                                \n";
    $stSql .= "    , folhapagamento.fgts                                       \n";
    $stSql .= "    , (SELECT cod_fgts                                          \n";
    $stSql .= "            , max(timestamp) as timestamp                       \n";
    $stSql .= "         FROM folhapagamento.fgts                               \n";
    $stSql .= "       GROUP BY cod_fgts) as max_fgts                           \n";
    $stSql .= "WHERE fgts_evento.cod_fgts   = fgts.cod_fgts                \n";
    $stSql .= "  AND fgts_evento.timestamp  = fgts.timestamp             \n";
    $stSql .= "  AND fgts.cod_fgts   = max_fgts.cod_fgts                        \n";
    $stSql .= "  AND fgts.timestamp  = max_fgts.timestamp                       \n";

    return $stSql;
}

function recuperaRelatorioFolhaAnaliticaSintetica(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY contrato.cod_contrato ";
    $stSql  = $this->montaRecuperaRelatorioFolhaAnaliticaSintetica().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioFolhaAnaliticaSintetica()
{
    $stSql  = "SELECT evento.cod_evento                                                             \n";
    $stSql .= "     , evento.codigo                                                                 \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                           \n";
    $stSql .= "     , evento.natureza                                                               \n";
    $stSql .= "     , evento_calculado.quantidade                                                   \n";
    $stSql .= "     , evento_calculado.valor                                                        \n";
    $stSql .= "     , contrato.cod_contrato                                                         \n";
    $stSql .= "     , contrato.registro                                                             \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                \n";
    $stSql .= "     , sw_cgm.numcgm                                                                 \n";
    $stSql .= "     , cod_orgao                                                                     \n";
    $stSql .= "  FROM folhapagamento.evento_calculado                                               \n";
    $stSql .= "     , folhapagamento.registro_evento                                                \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento                                         \n";
    $stSql .= "     , folhapagamento.registro_evento_periodo                                        \n";
    $stSql .= "     , folhapagamento.contrato_servidor_periodo                                      \n";

    $stSql .= "     , (SELECT servidor_contrato_servidor.cod_contrato                                                                          \n";
    $stSql .= "             , servidor.numcgm                                                                                                  \n";
    $stSql .= "          FROM pessoal.servidor_contrato_servidor                                                                               \n";
    $stSql .= "             , pessoal.servidor                                                                                                 \n";
    $stSql .= "         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                  \n";
    $stSql .= "         UNION                                                                                                                  \n";
    $stSql .= "        SELECT contrato_pensionista.cod_contrato                                                                                \n";
    $stSql .= "             , pensionista.numcgm                                                                                               \n";
    $stSql .= "          FROM pessoal.contrato_pensionista                                                                                     \n";
    $stSql .= "             , pessoal.pensionista                                                                                              \n";
    $stSql .= "         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                               \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor                        \n";

    $stSql .= "     LEFT JOIN (SELECT contrato_servidor_padrao.cod_contrato                         \n";
    $stSql .= "                     , contrato_servidor_padrao.cod_padrao                           \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_padrao                              \n";
    $stSql .= "                     , (  SELECT cod_contrato                                        \n";
    $stSql .= "                               , max(timestamp) as timestamp                         \n";
    $stSql .= "                            FROM pessoal.contrato_servidor_padrao                    \n";
    $stSql .= "                        GROUP BY cod_contrato) as max_contrato_servidor_padrao       \n";
    $stSql .= "                 WHERE contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato \n";
    $stSql .= "                   AND contrato_servidor_padrao.timestamp    = max_contrato_servidor_padrao.timestamp) as contrato_servidor_padrao    \n";
    $stSql .= "            ON servidor.cod_contrato = contrato_servidor_padrao.cod_contrato\n";

    $stSql .= "     LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                           \n";
    $stSql .= "                     , especialidade.cod_cargo as cod_funcao                                                                         \n";
    $stSql .= "                     , especialidade.cod_especialidade as cod_especialidade_funcao                                                   \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_especialidade_funcao                                                                \n";
    $stSql .= "                     , (  SELECT contrato_servidor_especialidade_funcao.cod_contrato                                                 \n";
    $stSql .= "                               , max(timestamp) as timestamp                                                                         \n";
    $stSql .= "                            FROM pessoal.contrato_servidor_especialidade_funcao                                                      \n";
    $stSql .= "                        GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao  \n";
    $stSql .= "                     , pessoal.especialidade                                                                                         \n";
    $stSql .= "                 WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato \n";
    $stSql .= "                   AND contrato_servidor_especialidade_funcao.timestamp    = max_contrato_servidor_especialidade_funcao.timestamp    \n";
    $stSql .= "                   AND contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_funcao \n";
    $stSql .= "            ON servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                  \n";

    $stSql .= "     LEFT JOIN (SELECT contrato_servidor_especialidade_cargo.cod_contrato                                                            \n";
    $stSql .= "                     , especialidade.cod_cargo                                                                                       \n";
    $stSql .= "                     , especialidade.cod_especialidade                                                                               \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_especialidade_cargo                                                                 \n";
    $stSql .= "                     , pessoal.especialidade                                                                                         \n";
    $stSql .= "                 WHERE contrato_servidor_especialidade_cargo.cod_especialidade = especialidade.cod_especialidade) as contrato_servidor_especialidade_cargo \n";
    $stSql .= "            ON servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato                                  \n";

    $stSql .= "     LEFT JOIN (SELECT contrato_servidor_orgao.cod_contrato                             \n";
    $stSql .= "                     , contrato_servidor_orgao.cod_orgao                                \n";
    $stSql .= "                     , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao                              \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_orgao                                   \n";
    $stSql .= "                     , (  SELECT cod_contrato                                            \n";
    $stSql .= "                               , max(timestamp) as timestamp                             \n";
    $stSql .= "                            FROM pessoal.contrato_servidor_orgao                         \n";
    $stSql .= "                        GROUP BY cod_contrato) as max_contrato_servidor_orgao            \n";
    $stSql .= "                     , organograma.orgao                                                 \n";
    $stSql .= "                 WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato \n";
    $stSql .= "                   AND contrato_servidor_orgao.timestamp    = max_contrato_servidor_orgao.timestamp \n";
    $stSql .= "                   AND contrato_servidor_orgao.cod_orgao    = orgao.cod_orgao ) as contrato_servidor_orgao    \n";
    $stSql .= "            ON servidor.cod_contrato = contrato_servidor_orgao.cod_contrato     \n";

    $stSql .= "LEFT JOIN (SELECT contrato_servidor_local.cod_local                                      \n";
    $stSql .= "                , contrato_servidor_local.cod_contrato                                   \n";
    $stSql .= "                , local.descricao as descricao_local                                     \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local                                        \n";
    $stSql .= "                , (  SELECT cod_contrato                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                  \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                              \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                 \n";
    $stSql .= "                , organograma.local                                                      \n";
    $stSql .= "            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                    \n";
    $stSql .= "              AND contrato_servidor_local.timestamp    = max_contrato_servidor_local.timestamp \n";
    $stSql .= "              AND contrato_servidor_local.cod_local    = local.cod_local) as contrato_servidor_local           \n";
    $stSql .= "              ON servidor.cod_contrato = contrato_servidor_local.cod_contrato   \n";
    $stSql .= "     , pessoal.contrato                                                              \n";
    $stSql .= "     , sw_cgm_pessoa_fisica                                                          \n";
    $stSql .= "     , sw_cgm                                                                        \n";
    $stSql .= "     , folhapagamento.evento                                                         \n";
    $stSql .= "     , folhapagamento.sequencia_calculo_evento                                       \n";
    $stSql .= "     , folhapagamento.sequencia_calculo                                              \n";
    $stSql .= " WHERE evento_calculado.cod_evento = registro_evento.cod_evento                      \n";
    $stSql .= "   AND evento_calculado.cod_registro = registro_evento.cod_registro                  \n";
    $stSql .= "   AND evento_calculado.timestamp_registro = registro_evento.timestamp               \n";
    $stSql .= "   AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento                \n";
    $stSql .= "   AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro            \n";
    $stSql .= "   AND registro_evento.timestamp  = ultimo_registro_evento.timestamp                 \n";
    $stSql .= "   AND registro_evento.cod_registro = registro_evento_periodo.cod_registro           \n";
    $stSql .= "   AND registro_evento_periodo.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao \n";
    $stSql .= "   AND registro_evento_periodo.cod_contrato = contrato_servidor_periodo.cod_contrato \n";
    $stSql .= "   AND contrato_servidor_periodo.cod_contrato = servidor.cod_contrato       \n";
    $stSql .= "   AND servidor.cod_contrato = contrato.cod_contrato                        \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                 \n";
    $stSql .= "   AND sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm                                   \n";
    $stSql .= "   AND registro_evento.cod_evento = evento.cod_evento                                \n";
    $stSql .= "   AND evento.cod_evento = sequencia_calculo_evento.cod_evento                       \n";
    $stSql .= "   AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia      \n";

    return $stSql;
}

function recuperaEventosBaseDescontoRelatorioFichaFinanceira(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY contrato.registro ";
    $stSql  = $this->montaRecuperaEventosBaseDescontoRelatorioFichaFinanceira().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosBaseDescontoRelatorioFichaFinanceira()
{
    $stSql  = "SELECT registro                                                                                                                                 \n";
    $stSql .= "     , codigo                                                                                                                                   \n";
    $stSql .= "     , cod_complementar                                                                                                                         \n";
    $stSql .= "     , descricao                                                                                                                                \n";
    $stSql .= "     , valor                                                                                                                                    \n";
    $stSql .= "     , desdobramento                                                                                                                            \n";
    $stSql .= "     , cod_tipo                                                                                                                                 \n";
    $stSql .= "  FROM (SELECT *                                                                                                                                \n";
    $stSql .= "          FROM (SELECT contrato.registro                                                                                                        \n";
    $stSql .= "                     , evento.codigo                                                                                                            \n";
    $stSql .= "                     , registro_evento_complementar.cod_complementar                                                                            \n";
    $stSql .= "                     , trim(evento.descricao) as descricao                                                                                      \n";
    $stSql .= "                     , evento_complementar_calculado.valor as valor                                                                             \n";
    $stSql .= "                     , numcgm                                                                                                                   \n";
    $stSql .= "                     , natureza                                                                                                                 \n";
    $stSql .= "                     , cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "                     , null as desdobramento                                                                                                    \n";
    $stSql .= "                     , cod_tipo                                                                                                                 \n";
    $stSql .= "                  FROM folhapagamento.evento_complementar_calculado                                                                             \n";
    $stSql .= "                     , folhapagamento.registro_evento_complementar                                                                              \n";
    $stSql .= "                     , folhapagamento.ultimo_registro_evento_complementar                                                                       \n";
    $stSql .= "                     , folhapagamento.evento                                                                                                    \n";
    $stSql .= "                     , (SELECT servidor_contrato_servidor.cod_contrato                                                                          \n";
    $stSql .= "                             , servidor.numcgm                                                                                                  \n";
    $stSql .= "                          FROM pessoal.servidor_contrato_servidor                                                                               \n";
    $stSql .= "                             , pessoal.servidor                                                                                                 \n";
    $stSql .= "                         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                  \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT contrato_pensionista.cod_contrato                                                                                \n";
    $stSql .= "                             , pensionista.numcgm                                                                                               \n";
    $stSql .= "                          FROM pessoal.contrato_pensionista                                                                                     \n";
    $stSql .= "                             , pessoal.pensionista                                                                                              \n";
    $stSql .= "                         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                               \n";
    $stSql .= "                           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor                        \n";
    $stSql .= "                     , pessoal.contrato                                                                                                         \n";
    $stSql .= "                     , (SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 7                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 4                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 5                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 6                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 3                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT previdencia_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , previdencia_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.previdencia_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_previdencia                                                                                        \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.previdencia_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_previdencia) as max_previdencia_evento                                                             \n";
    $stSql .= "                             , folhapagamento.tipo_evento_previdencia                                                                           \n";
    $stSql .= "                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                   \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                      \n";
    $stSql .= "                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                 \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = ".$this->getDado("cod_previdencia")."                                      \n";
    $stSql .= "                           AND tipo_evento_previdencia.cod_tipo = 2                                                                             \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT previdencia_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , previdencia_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.previdencia_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_previdencia                                                                                        \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.previdencia_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_previdencia) as max_previdencia_evento                                                             \n";
    $stSql .= "                             , folhapagamento.tipo_evento_previdencia                                                                           \n";
    $stSql .= "                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                   \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                      \n";
    $stSql .= "                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                 \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = ".$this->getDado("cod_previdencia")."                                      \n";
    $stSql .= "                           AND tipo_evento_previdencia.cod_tipo = 1) as eventos_base                                                            \n";
    $stSql .= "                 WHERE registro_evento_complementar.cod_evento     = ultimo_registro_evento_complementar.cod_evento                             \n";
    $stSql .= "                   AND registro_evento_complementar.cod_registro   = ultimo_registro_evento_complementar.cod_registro                           \n";
    $stSql .= "                   AND registro_evento_complementar.timestamp      = ultimo_registro_evento_complementar.timestamp                              \n";
    $stSql .= "                   AND registro_evento_complementar.cod_evento     = evento_complementar_calculado.cod_evento                                   \n";
    $stSql .= "                   AND registro_evento_complementar.cod_registro   = evento_complementar_calculado.cod_registro                                 \n";
    $stSql .= "                   AND registro_evento_complementar.timestamp      = evento_complementar_calculado.timestamp_registro                           \n";
    $stSql .= "                   AND registro_evento_complementar.cod_evento     = eventos_base.cod_evento                                                    \n";
    $stSql .= "                   AND registro_evento_complementar.cod_evento     = evento.cod_evento                                                          \n";
    $stSql .= "                   AND registro_evento_complementar.cod_contrato   = servidor.cod_contrato                                                      \n";
    $stSql .= "                   AND EXISTS (SELECT *\n";
    $stSql .= "                                 FROM folhapagamento.complementar\n";
    $stSql .= "                                   , folhapagamento.complementar_situacao\n";
    $stSql .= "                                   , (SELECT cod_periodo_movimentacao\n";
    $stSql .= "                                   , cod_complementar\n";
    $stSql .= "                                   ,  max(timestamp) as timestamp\n";
    $stSql .= "                                FROM folhapagamento.complementar_situacao\n";
    $stSql .= "                            GROUP BY cod_periodo_movimentacao\n";
    $stSql .= "                                   , cod_complementar) as max_complementar_situacao\n";
    $stSql .= "                               WHERE complementar.cod_complementar = complementar_situacao.cod_complementar\n";
    $stSql .= "                                 AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar\n";
    $stSql .= "                                 AND complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao\n";
    $stSql .= "                                 AND complementar_situacao.timestamp = max_complementar_situacao.timestamp\n";
    $stSql .= "                                 AND complementar_situacao.situacao = 'f'\n";
    $stSql .= "                                 AND complementar_situacao.cod_complementar = registro_evento_complementar.cod_complementar\n";
    $stSql .= "                                 AND complementar_situacao.cod_periodo_movimentacao = registro_evento_complementar.cod_periodo_movimentacao)    \n";

    $stSql .= "                   AND servidor.cod_contrato = contrato.cod_contrato) as complementar                                                           \n";
    $stSql .= "        UNION                                                                                                                                   \n";
    $stSql .= "        SELECT *                                                                                                                                \n";
    $stSql .= "          FROM (SELECT contrato.registro                                                                                                        \n";
    $stSql .= "                     , evento.codigo                                                                                                            \n";
    $stSql .= "                     , -1 as cod_complementar                                                                                                   \n";
    $stSql .= "                     , trim(evento.descricao) as descricao                                                                                      \n";
    $stSql .= "                     , evento_ferias_calculado.valor as valor                                                                                   \n";
    $stSql .= "                     , numcgm                                                                                                                   \n";
    $stSql .= "                     , natureza                                                                                                                 \n";
    $stSql .= "                     , cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "                     , evento_ferias_calculado.desdobramento as desdobramento                                                                   \n";
    $stSql .= "                     , cod_tipo                                                                                                                 \n";
    $stSql .= "                  FROM folhapagamento.evento_ferias_calculado                                                                                   \n";
    $stSql .= "                     , folhapagamento.registro_evento_ferias                                                                                    \n";
    $stSql .= "                     , folhapagamento.ultimo_registro_evento_ferias                                                                             \n";
    $stSql .= "                     , folhapagamento.evento                                                                                                    \n";
    $stSql .= "                     , (SELECT servidor_contrato_servidor.cod_contrato                                                                          \n";
    $stSql .= "                             , servidor.numcgm                                                                                                  \n";
    $stSql .= "                          FROM pessoal.servidor_contrato_servidor                                                                               \n";
    $stSql .= "                             , pessoal.servidor                                                                                                 \n";
    $stSql .= "                         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                  \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT contrato_pensionista.cod_contrato                                                                                \n";
    $stSql .= "                             , pensionista.numcgm                                                                                               \n";
    $stSql .= "                          FROM pessoal.contrato_pensionista                                                                                     \n";
    $stSql .= "                             , pessoal.pensionista                                                                                              \n";
    $stSql .= "                         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                               \n";
    $stSql .= "                           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor                        \n";
    $stSql .= "                     , pessoal.contrato                                                                                                         \n";
    $stSql .= "                     , (SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 7                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 4                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 5                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 6                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 3                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT previdencia_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , previdencia_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.previdencia_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_previdencia                                                                                        \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.previdencia_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_previdencia) as max_previdencia_evento                                                             \n";
    $stSql .= "                             , folhapagamento.tipo_evento_previdencia                                                                           \n";
    $stSql .= "                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                   \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                      \n";
    $stSql .= "                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                 \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = ".$this->getDado("cod_previdencia")."                                      \n";
    $stSql .= "                           AND tipo_evento_previdencia.cod_tipo = 2                                                                             \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT previdencia_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , previdencia_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.previdencia_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_previdencia                                                                                        \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.previdencia_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_previdencia) as max_previdencia_evento                                                             \n";
    $stSql .= "                             , folhapagamento.tipo_evento_previdencia                                                                           \n";
    $stSql .= "                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                   \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                      \n";
    $stSql .= "                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                 \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = ".$this->getDado("cod_previdencia")."                                      \n";
    $stSql .= "                           AND tipo_evento_previdencia.cod_tipo = 1) as eventos_base                                                            \n";
    $stSql .= "                 WHERE registro_evento_ferias.cod_evento     = ultimo_registro_evento_ferias.cod_evento                                         \n";
    $stSql .= "                   AND registro_evento_ferias.cod_registro   = ultimo_registro_evento_ferias.cod_registro                                       \n";
    $stSql .= "                   AND registro_evento_ferias.timestamp      = ultimo_registro_evento_ferias.timestamp                                          \n";
    $stSql .= "                   AND registro_evento_ferias.desdobramento      = ultimo_registro_evento_ferias.desdobramento                                  \n";
    $stSql .= "                   AND registro_evento_ferias.cod_evento     = evento_ferias_calculado.cod_evento                                               \n";
    $stSql .= "                   AND registro_evento_ferias.cod_registro   = evento_ferias_calculado.cod_registro                                             \n";
    $stSql .= "                   AND registro_evento_ferias.timestamp      = evento_ferias_calculado.timestamp_registro                                       \n";
    $stSql .= "                   AND registro_evento_ferias.desdobramento      = evento_ferias_calculado.desdobramento                                        \n";
    $stSql .= "                   AND registro_evento_ferias.cod_evento     = eventos_base.cod_evento                                                          \n";
    $stSql .= "                   AND registro_evento_ferias.cod_evento     = evento.cod_evento                                                                \n";
    $stSql .= "                   AND registro_evento_ferias.cod_contrato = servidor.cod_contrato                                                              \n";
    $stSql .= "                   AND EXISTS (SELECT ferias.cod_contrato                                                                                       \n";
    $stSql .= "                                 FROM pessoal.ferias                                                                                            \n";
    $stSql .= "                                   , pessoal.lancamento_ferias                                                                                  \n";
    $stSql .= "                                   , folhapagamento.periodo_movimentacao                                                                        \n";
    $stSql .= "                               WHERE ferias.cod_ferias = lancamento_ferias.cod_ferias                                                           \n";
    $stSql .= "                                 AND ferias.cod_contrato = registro_evento_ferias.cod_contrato                                                  \n";
    $stSql .= "                                 AND periodo_movimentacao.cod_periodo_movimentacao = registro_evento_ferias.cod_periodo_movimentacao            \n";
    $stSql .= "                                 AND lancamento_ferias.ano_competencia = to_char(periodo_movimentacao.dt_final,'yyyy')                          \n";
    $stSql .= "                                 AND lancamento_ferias.mes_competencia = to_char(periodo_movimentacao.dt_final,'mm'))                           \n";
    $stSql .= "                   AND servidor.cod_contrato = contrato.cod_contrato) as ferias                                                                 \n";
    $stSql .= "        UNION                                                                                                                                   \n";
    $stSql .= "        SELECT *                                                                                                                                \n";
    $stSql .= "          FROM (SELECT contrato.registro                                                                                                        \n";
    $stSql .= "                     , evento.codigo                                                                                                            \n";
    $stSql .= "                     , 0 as cod_complementar                                                                                                    \n";
    $stSql .= "                     , trim(evento.descricao) as descricao                                                                                      \n";
    $stSql .= "                     , evento_calculado.valor as valor                                                                                          \n";
    $stSql .= "                     , numcgm                                                                                                                   \n";
    $stSql .= "                     , natureza                                                                                                                 \n";
    $stSql .= "                     , cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "                     , desdobramento                                                                                                    \n";
    $stSql .= "                     , cod_tipo                                                                                                                 \n";
    $stSql .= "                  FROM folhapagamento.evento_calculado                                                                                          \n";
    $stSql .= "                     , folhapagamento.registro_evento                                                                                           \n";
    $stSql .= "                     , folhapagamento.ultimo_registro_evento                                                                                    \n";
    $stSql .= "                     , folhapagamento.registro_evento_periodo                                                                                   \n";
    $stSql .= "                     , folhapagamento.evento                                                                                                    \n";
    $stSql .= "                     , (SELECT servidor_contrato_servidor.cod_contrato                                                                          \n";
    $stSql .= "                             , servidor.numcgm                                                                                                  \n";
    $stSql .= "                          FROM pessoal.servidor_contrato_servidor                                                                               \n";
    $stSql .= "                             , pessoal.servidor                                                                                                 \n";
    $stSql .= "                         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                  \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT contrato_pensionista.cod_contrato                                                                                \n";
    $stSql .= "                             , pensionista.numcgm                                                                                               \n";
    $stSql .= "                          FROM pessoal.contrato_pensionista                                                                                     \n";
    $stSql .= "                             , pessoal.pensionista                                                                                              \n";
    $stSql .= "                         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                               \n";
    $stSql .= "                           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor                        \n";
    $stSql .= "                     , pessoal.contrato                                                                                                         \n";
    $stSql .= "                     , (SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 7                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 4                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 5                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 6                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT tabela_irrf_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , tabela_irrf_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.tabela_irrf_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_tabela                                                                                             \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.tabela_irrf_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                  \n";
    $stSql .= "                             , folhapagamento.tipo_evento_irrf                                                                                  \n";
    $stSql .= "                         WHERE tabela_irrf_evento.cod_tipo = tipo_evento_irrf.cod_tipo                                                          \n";
    $stSql .= "                           AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                \n";
    $stSql .= "                           AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp                                                 \n";
    $stSql .= "                           AND tipo_evento_irrf.cod_tipo = 3                                                                                    \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT previdencia_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , previdencia_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.previdencia_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_previdencia                                                                                        \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.previdencia_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_previdencia) as max_previdencia_evento                                                             \n";
    $stSql .= "                             , folhapagamento.tipo_evento_previdencia                                                                           \n";
    $stSql .= "                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                   \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                      \n";
    $stSql .= "                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                 \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = ".$this->getDado("cod_previdencia")."                                      \n";
    $stSql .= "                           AND tipo_evento_previdencia.cod_tipo = 2                                                                             \n";
    $stSql .= "                         UNION                                                                                                                  \n";
    $stSql .= "                        SELECT previdencia_evento.cod_evento                                                                                    \n";
    $stSql .= "                             , previdencia_evento.cod_tipo                                                                                      \n";
    $stSql .= "                          FROM folhapagamento.previdencia_evento                                                                                \n";
    $stSql .= "                             , (  SELECT cod_previdencia                                                                                        \n";
    $stSql .= "                                       , max(timestamp) as timestamp                                                                            \n";
    $stSql .= "                                    FROM folhapagamento.previdencia_evento                                                                      \n";
    $stSql .= "                                GROUP BY cod_previdencia) as max_previdencia_evento                                                             \n";
    $stSql .= "                             , folhapagamento.tipo_evento_previdencia                                                                           \n";
    $stSql .= "                         WHERE previdencia_evento.cod_tipo = tipo_evento_previdencia.cod_tipo                                                   \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                      \n";
    $stSql .= "                           AND previdencia_evento.timestamp  = max_previdencia_evento.timestamp                                                 \n";
    $stSql .= "                           AND previdencia_evento.cod_previdencia = ".$this->getDado("cod_previdencia")."                                      \n";
    $stSql .= "                           AND tipo_evento_previdencia.cod_tipo = 1) as eventos_base                                                            \n";
    $stSql .= "                 WHERE registro_evento.cod_evento     = ultimo_registro_evento.cod_evento                                                       \n";
    $stSql .= "                   AND registro_evento.cod_registro   = ultimo_registro_evento.cod_registro                                                     \n";
    $stSql .= "                   AND registro_evento.timestamp      = ultimo_registro_evento.timestamp                                                        \n";
    $stSql .= "                   AND registro_evento.cod_evento     = evento_calculado.cod_evento                                                             \n";
    $stSql .= "                   AND registro_evento.cod_registro   = evento_calculado.cod_registro                                                           \n";
    $stSql .= "                   AND registro_evento.timestamp      = evento_calculado.timestamp_registro                                                     \n";
    $stSql .= "                   AND registro_evento.cod_registro   = registro_evento_periodo.cod_registro                                                    \n";
    $stSql .= "                   AND registro_evento.cod_evento     = eventos_base.cod_evento                                                                 \n";
    $stSql .= "                   AND registro_evento.cod_evento     = evento.cod_evento                                                                       \n";
    $stSql .= "                   AND registro_evento_periodo.cod_contrato = servidor.cod_contrato                                                             \n";
    $stSql .= "                   AND servidor.cod_contrato = contrato.cod_contrato) as salario) as eventos                                                    \n";

    return $stSql;
}

#######################################################################################
#                                                                                     #
#          C O N S U L T A S   P A R A   R E M E S S A   B A N C A R I A              #
#                                                                                     #
#######################################################################################

function recuperaContratosCalculadosRemessaBancos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    if (trim($stOrdem)=="") {$stOrdem=" ORDER BY nom_cgm ";}
    $obErro = $this->executaRecupera("montaRecuperaContratosCalculadosRemessaBancos",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaContratosCalculadosRemessaBancos()
{
    $stSqlDesdobramento = "";
    if ($this->getDado("stDesdobramento")!="") {
        $stSqlDesdobramento = "\n AND desdobramento = '".$this->getDado("stDesdobramento")."'";
    }

    $stSql  = "\n SELECT *";
    $stSql .= "\n   FROM (";
    $stSql .= "\n SELECT servidor_pensionista.* ";
    $stSql .= "\n      , eventos_calculados.proventos ";
    $stSql .= "\n      , eventos_calculados.descontos ";

    if ( $this->getDado('nuPercentualPagar') != "") {
        $stSql .= "\n      , CASE WHEN eventos_calculados.proventos - eventos_calculados.descontos > 0 THEN ";
        $stSql .= "\n                  ((eventos_calculados.proventos - eventos_calculados.descontos) * ".$this->getDado('nuPercentualPagar').") / 100 ";
        $stSql .= "\n             ELSE ";
        $stSql .= "\n                  0 ";
        $stSql .= "\n        END as liquido";
    } else {
        $stSql .= "\n      , (eventos_calculados.proventos - eventos_calculados.descontos) as liquido ";
    }

    $stSql .= "\n   FROM ( ";

    if ( $this->getDado("stSituacao") != "pensionistas" ) {
        $stSql .= "\n          SELECT 'S' as tipo_cadastro ";
        $stSql .= "\n               , servidor.cod_contrato ";
        $stSql .= "\n               , servidor.nom_cgm ";
        $stSql .= "\n               , servidor.cpf ";
        $stSql .= "\n               , servidor.registro ";
        $stSql .= "\n               , servidor.nr_conta_salario as nr_conta ";
        $stSql .= "\n               , servidor.num_banco_salario as num_banco ";
        $stSql .= "\n               , servidor.cod_banco_salario as cod_banco";
        $stSql .= "\n               , servidor.num_agencia_salario as num_agencia";
        $stSql .= "\n               , servidor.cod_orgao ";
        $stSql .= "\n               , servidor.cod_local ";
        $stSql .= "\n               , servidor.desc_cargo as descricao_cargo ";
        $stSql .= "\n               , servidor.desc_funcao as descricao_funcao ";
        $stSql .= "\n            FROM recuperarContratoServidor('cgm,cs,o,l,f,ca','".Sessao::getEntidade()."',".$this->getDado("inCodPeriodoMovimentacao").",'".$this->getDado("stTipoFiltro")."','".$this->getDado("stValoresFiltro")."','".Sessao::getExercicio()."') as servidor ";
        $stSql .= "\n           WHERE servidor.nr_conta_salario IS NOT NULL";//adicionado esse parametro para filtrar os servidores que recebam por crédito em conta
        $stSql .= "\n             AND servidor.num_banco_salario IS NOT NULL ";
        $stSql .= "\n             AND servidor.cod_banco_salario IS NOT NULL";
        $stSql .= "\n             AND servidor.num_agencia_salario IS NOT NULL";
    }

    if ( $this->getDado("stSituacao") == "pensionistas" || $this->getDado("stSituacao") == "todos") {

        if ( $this->getDado("stSituacao") == "todos") {
            $stSql .= "\n  UNION ";
        }
        $stSql .= "\n          SELECT 'P' as tipo_cadastro ";
        $stSql .= "\n               , pensionista.cod_contrato ";
        $stSql .= "\n               , pensionista.nom_cgm ";
        $stSql .= "\n               , pensionista.cpf ";
        $stSql .= "\n               , pensionista.registro ";
        $stSql .= "\n               , pensionista.nr_conta_salario as nr_conta";
        $stSql .= "\n               , pensionista.num_banco_salario as num_banco";
        $stSql .= "\n               , pensionista.cod_banco_salario as cod_banco";
        $stSql .= "\n               , pensionista.num_agencia_salario as num_agencia";
        $stSql .= "\n               , pensionista.cod_orgao";
        $stSql .= "\n               , pensionista.cod_local ";
        $stSql .= "\n               , null as descricao_cargo ";
        $stSql .= "\n               , null as descricao_funcao ";
        $stSql .= "\n            FROM recuperarContratoPensionista('cgm,cs,o,l','".Sessao::getEntidade()."',".$this->getDado("inCodPeriodoMovimentacao").",'".$this->getDado("stTipoFiltro")."','".$this->getDado("stValoresFiltro")."','".Sessao::getExercicio()."') as pensionista ";

    }
    $stSql .= "\n     ) as servidor_pensionista ";

    $stSql .= "\nINNER JOIN (   SELECT cod_contrato ";
    $stSql .= "\n                    , coalesce(sum(proventos),0) as proventos";
    $stSql .= "\n                    , coalesce(sum(descontos),0) as descontos";
    $stSql .= "\n                 FROM (";
    $stSql .= "\n                           SELECT cod_contrato";
    $stSql .= "\n                                , sum(valor) as proventos";
    $stSql .= "\n                                , 0 as descontos";
    $stSql .= "\n                             FROM recuperarEventosCalculados(".$this->getDado("inCodConfiguracao").",".$this->getDado("inCodPeriodoMovimentacao").",0,".$this->getDado("inCodComplementar").",'".Sessao::getEntidade()."', 'evento.descricao') as eventos_proventos";
    $stSql .= "\n                            WHERE ";

    if ( is_array($this->getDado('arEventosProventos')) ) {
        $stSql .= " eventos_proventos.cod_evento IN (".implode(",",$this->getDado('arEventosProventos')).") ";
    } else {
        $stSql .= " eventos_proventos.natureza = 'P' ";
    }

    $stSql .= "\n                              ".$stSqlDesdobramento;
    $stSql .= "\n                         GROUP BY eventos_proventos.cod_contrato";
    $stSql .= "\n                            UNION ";
    $stSql .= "\n                           SELECT cod_contrato";
    $stSql .= "\n                                , 0 as proventos";
    $stSql .= "\n                                , sum(valor) as descontos";
    $stSql .= "\n                             FROM recuperarEventosCalculados(".$this->getDado("inCodConfiguracao").",".$this->getDado("inCodPeriodoMovimentacao").",0,".$this->getDado("inCodComplementar").",'".Sessao::getEntidade()."', 'evento.descricao') as eventos_descontos";
    $stSql .= "\n                            WHERE ";

    if ( is_array($this->getDado('arEventosDescontos')) ) {
        $stSql .= " eventos_descontos.cod_evento IN (".implode(",",$this->getDado('arEventosDescontos')).") ";
    } else {
        $stSql .= " eventos_descontos.natureza = 'D' ";
    }

    $stSql .= "\n                              ".$stSqlDesdobramento;
    $stSql .= "\n                         GROUP BY eventos_descontos.cod_contrato";
    $stSql .= "\n                        ) as eventos_calculados_proventos_descontos_contrato";
    $stSql .= "\n               GROUP BY cod_contrato";
    $stSql .= "\n             ) as eventos_calculados";
    $stSql .= "\n          ON servidor_pensionista.cod_contrato = eventos_calculados.cod_contrato";
    $stSql .= "\n ) as remessa ";

    $stSqlFiltro = "";

    switch ( $this->getDado("stSituacao") ) {
        case "ativos":
            $stSqlFiltro .= "AND recuperarSituacaoDoContrato(remessa.cod_contrato, '".$this->getDado("inCodPeriodoMovimentacao")."', '".Sessao::getEntidade()."') = 'A'";
            break;
        case "aposentados":
            $stSqlFiltro .= "AND recuperarSituacaoDoContrato(remessa.cod_contrato, '".$this->getDado("inCodPeriodoMovimentacao")."', '".Sessao::getEntidade()."') = 'P'";
            break;
        case "rescindidos":
            $stSqlFiltro .= "AND recuperarSituacaoDoContrato(remessa.cod_contrato, '".$this->getDado("inCodPeriodoMovimentacao")."', '".Sessao::getEntidade()."') = 'R'";
            break;
        case "pensionistas":
            $stSqlFiltro .= "AND recuperarSituacaoDoContrato(remessa.cod_contrato, '".$this->getDado("inCodPeriodoMovimentacao")."', '".Sessao::getEntidade()."') = 'E'";
            break;
        case "todos":
            $stSqlFiltro .= "AND recuperarSituacaoDoContrato(remessa.cod_contrato, '".$this->getDado("inCodPeriodoMovimentacao")."', '".Sessao::getEntidade()."') IN ('A','P','R','E')";
            break;
    }

    if ($this->getDado('inCodBanco') != "") {
        //Se for passado apenas um ID executa id, senão executa else onde os id's são inseridos dentro do IN
        if (is_numeric($this->getDado('inCodBanco'))) {
            $stSqlFiltro .= " AND remessa.cod_banco = ".$this->getDado('inCodBanco');
        } else {
            $stSqlFiltro .= " AND remessa.cod_banco IN (".$this->getDado('inCodBanco').")";
        }
    }

    if ($this->getDado('nuLiquidoMinimo') != "" && $this->getDado('nuLiquidoMaximo') != "") {
        $stSqlFiltro .= " AND (remessa.proventos - remessa.descontos) BETWEEN ".$this->getDado('nuLiquidoMinimo')." AND ".$this->getDado('nuLiquidoMaximo');
    }

    $stSqlFiltro .= " AND remessa.liquido > 0 ";

    $stSql .= " WHERE ".substr($stSqlFiltro, 4);

    return $stSql;
}


function recuperaProventosEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaProventosEsfinge().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaProventosEsfinge()
{
   $stSql = "
select contrato_servidor_caso_causa_norma.cod_norma
      ,contrato.registro
      ,to_char(periodo_movimentacao.dt_inicial, 'yyyymm') as ano_mes_referencia
      ,case evento.natureza
         when 'P' then 1
         when 'D' then 2
       end as natureza
      ,evento_calculado.cod_evento
      ,evento.descricao
      ,evento_calculado.valor
from folhapagamento.evento_calculado
join folhapagamento.evento
  on evento.cod_evento = evento_calculado.cod_evento
join folhapagamento.registro_evento_periodo
  on registro_evento_periodo.cod_registro = evento_calculado.cod_registro
join folhapagamento.periodo_movimentacao
  on periodo_movimentacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao
join pessoal.contrato_servidor
  on contrato_servidor.cod_contrato = registro_evento_periodo.cod_contrato
join pessoal.contrato
  on contrato.cod_contrato = contrato_servidor.cod_contrato
join pessoal.contrato_servidor_caso_causa
  on contrato_servidor_caso_causa.cod_contrato = contrato_servidor.cod_contrato
join pessoal.contrato_servidor_caso_causa_norma
  on contrato_servidor_caso_causa_norma.cod_contrato = contrato_servidor_caso_causa.cod_contrato
join pessoal.contrato_servidor_previdencia
  on contrato_servidor_previdencia.cod_contrato = contrato_servidor.cod_contrato
join folhapagamento.previdencia
  on previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
where previdencia.cod_regime_previdencia = 2
  and evento.natureza in ('P', 'D')
  and periodo_movimentacao.dt_inicial >= to_date('dd/mm/yyy','".$this->getDado("dt_inicial")."')
  and periodo_movimentacao.dt_final <= to_date('dd/mm/yyy','".$this->getDado('dt_final')."')
";

   return $stSql;

}

function recuperaEventosCalculadosRais(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaEventosCalculadosRais",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaEventosCalculadosRais()
{
    $stSql  = "SELECT sum(evento_calculado.valor) as valor                                                     \n";
    $stSql .= "     , sum(evento_calculado.quantidade) as quantidade                                           \n";
    $stSql .= "  FROM folhapagamento.registro_evento_periodo                         \n";
    $stSql .= "     , folhapagamento.evento_calculado                                \n";
    $stSql .= "     , folhapagamento.periodo_movimentacao                            \n";
    $stSql .= " WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro                     \n";
    $stSql .= "   AND registro_evento_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao  \n";

    return $stSql;
}

function recuperaContratosFichaFinanceiraSalario(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarContratosFichaFinanceiraSalario().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperarContratosFichaFinanceiraSalario()
{
    $stSql  = "SELECT to_real(evento_calculado.valor) as valor                                                                                                     \n";
    $stSql .= "     , to_real(evento_calculado.quantidade) as quantidade                                                                                           \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_periodo.cod_contrato) as matricula     \n";
    $stSql .= "     , registro_evento_periodo.cod_contrato                                                                                                         \n";
    $stSql .= "     , servidor.numcgm                                                                                                                              \n";
    $stSql .= "     , ( case when evento_calculado.desdobramento = 'A'  then 'Abono'                                                                         \n";
    $stSql .= "              when evento_calculado.desdobramento = 'F'  then 'Férias'                                                                        \n";
    $stSql .= "              when evento_calculado.desdobramento = 'D'  then 'Adiantamento'                                                                  \n";
    $stSql .= "        end ) as descricao                                                                                                                          \n";
    $stSql .= "     , registro_evento_periodo.cod_periodo_movimentacao                                                                                             \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                       \n";
    $stSql .= " FROM folhapagamento.registro_evento_periodo                                                                              \n";
    $stSql .= "     , folhapagamento.registro_evento                                                                                     \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento                                                                              \n";
    $stSql .= "     , folhapagamento.evento_calculado                                                                                    \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                 \n";
    $stSql .= "     , pessoal.servidor                                                                                                   \n";
    $stSql .= "     , folhapagamento.evento                                                                                              \n";
    $stSql .= " WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro                                                                          \n";
    $stSql .= "     AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro                                                                         \n";
    $stSql .= "     AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento                                                                             \n";
    $stSql .= "     AND registro_evento.timestamp = ultimo_registro_evento.timestamp                                                                               \n";
    $stSql .= "     AND ultimo_registro_evento.cod_registro = evento_calculado.cod_registro                                                                        \n";
    $stSql .= "     AND ultimo_registro_evento.cod_evento = evento_calculado.cod_evento                                                                            \n";
    $stSql .= "     AND ultimo_registro_evento.timestamp = evento_calculado.timestamp_registro                                                                     \n";
    $stSql .= "     AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato                                                             \n";
    $stSql .= "     AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                            \n";
    $stSql .= "     AND evento_calculado.cod_evento = evento.cod_evento                                                                                            \n";

    return $stSql;
}

function recuperaContratosFichaFinanceiraSalarioComPensionista(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarContratosFichaFinanceiraSalarioComPensionista($stFiltro,$stOrdem);
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperarContratosFichaFinanceiraSalarioComPensionista($stFiltro,$stOrdem)
{
    $stSql  = "SELECT to_real(evento_calculado.valor) as valor                                                                                                     \n";
    $stSql .= "     , ( case when evento.apresenta_parcela = false then to_real(evento_calculado.quantidade)                                                 \n";
    $stSql .= "              when evento.apresenta_parcela = true then evento_calculado.quantidade::integer||'/'||registro_evento_parcela.parcela::integer   \n";
    $stSql .= "        end ) as quantidade                                                                                                                   \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_periodo.cod_contrato) as matricula     \n";
    $stSql .= "     , registro_evento_periodo.cod_contrato                                                                                                         \n";
    $stSql .= "     , servidor.numcgm                                                                                                                              \n";
    $stSql .= "     , ( case when evento_calculado.desdobramento = 'A'  then 'Abono'                                                                         \n";
    $stSql .= "              when evento_calculado.desdobramento = 'F'  then 'Férias'                                                                        \n";
    $stSql .= "              when evento_calculado.desdobramento = 'D'  then 'Adiantamento'                                                                  \n";
    $stSql .= "        end ) as descricao                                                                                                                          \n";
    $stSql .= "     , registro_evento_periodo.cod_periodo_movimentacao                                                                                             \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                       \n";
    $stSql .= " FROM folhapagamento.registro_evento_periodo                                                                              \n";
    $stSql .= "     , folhapagamento.registro_evento                                                                                     \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento                                                                              \n";
    $stSql .= "      LEFT JOIN folhapagamento.registro_evento_parcela                                                                   \n";
    $stSql .= "              ON registro_evento_parcela.cod_evento = ultimo_registro_evento.cod_evento                                   \n";
    $stSql .= "             AND registro_evento_parcela.cod_registro = ultimo_registro_evento.cod_registro                               \n";
    $stSql .= "             AND registro_evento_parcela.timestamp = ultimo_registro_evento.timestamp                                     \n";
    $stSql .= "     , folhapagamento.evento_calculado                                                                                    \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                 \n";
    $stSql .= "     , pessoal.servidor                                                                                                   \n";
    $stSql .= "     , folhapagamento.evento                                                                                              \n";
    $stSql .= " WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro                                                                          \n";
    $stSql .= "     AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro                                                                         \n";
    $stSql .= "     AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento                                                                             \n";
    $stSql .= "     AND registro_evento.timestamp = ultimo_registro_evento.timestamp                                                                               \n";
    $stSql .= "     AND ultimo_registro_evento.cod_registro = evento_calculado.cod_registro                                                                        \n";
    $stSql .= "     AND ultimo_registro_evento.cod_evento = evento_calculado.cod_evento                                                                            \n";
    $stSql .= "     AND ultimo_registro_evento.timestamp = evento_calculado.timestamp_registro                                                                     \n";
    $stSql .= "     AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato                                                             \n";
    $stSql .= "     AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                            \n";
    $stSql .= "     AND evento_calculado.cod_evento = evento.cod_evento                                                                                            \n";
    $stSql .= $stFiltro;

    //adicionada união para buscar pensionista
    $stSql .= "    UNION                                                                                                                                           \n";
    $stSql .= " SELECT to_real(evento_calculado.valor) as valor                                                                                                    \n";
    $stSql .= "     , ( case when evento.apresenta_parcela = false then to_real(evento_calculado.quantidade)                                                       \n";
    $stSql .= "              when evento.apresenta_parcela = true then evento_calculado.quantidade::integer||'/'||registro_evento_parcela.parcela::integer         \n";
    $stSql .= "        end ) as quantidade                                                                                                                         \n";
    $stSql .= "      , (select registro from pessoal.contrato where cod_contrato = registro_evento_periodo.cod_contrato) as matricula                              \n";
    $stSql .= "      , registro_evento_periodo.cod_contrato                                                                                                        \n";
    $stSql .= "      , pensionista.numcgm                                                                                                                          \n";
    $stSql .= "      , ( case when evento_calculado.desdobramento = 'A'  then 'Abono'                                                                              \n";
    $stSql .= "               when evento_calculado.desdobramento = 'F'  then 'Férias'                                                                             \n";
    $stSql .= "               when evento_calculado.desdobramento = 'D'  then 'Adiantamento'                                                                       \n";
    $stSql .= "         end ) as descricao                                                                                                                         \n";
    $stSql .= "      , registro_evento_periodo.cod_periodo_movimentacao                                                                                            \n";
    $stSql .= "      , (select nom_cgm from sw_cgm where numcgm = pensionista.numcgm) as nom_cgm                                                                   \n";
    $stSql .= "  FROM folhapagamento.registro_evento_periodo                                                                                                       \n";
    $stSql .= "  INNER JOIN pessoal.contrato_pensionista                                                                                                           \n";
    $stSql .= "          ON registro_evento_periodo.cod_contrato = contrato_pensionista.cod_contrato                                                               \n";
    $stSql .= "  INNER JOIN pessoal.pensionista                                                                                                                    \n";
    $stSql .= "          ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                     \n";
    $stSql .= "         AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                                                           \n";
    $stSql .= "  INNER JOIN folhapagamento.registro_evento                                                                                                         \n";
    $stSql .= "          ON registro_evento_periodo.cod_registro = registro_evento.cod_registro                                                                    \n";
    $stSql .= "  INNER JOIN folhapagamento.ultimo_registro_evento                                                                                                  \n";
    $stSql .= "          ON registro_evento.cod_registro = ultimo_registro_evento.cod_registro                                                                     \n";
    $stSql .= "         AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento                                                                         \n";
    $stSql .= "         AND registro_evento.timestamp = ultimo_registro_evento.timestamp                                                                           \n";
    $stSql .= "      LEFT JOIN folhapagamento.registro_evento_parcela                                                                    \n";
    $stSql .= "              ON registro_evento_parcela.cod_evento = ultimo_registro_evento.cod_evento                                   \n";
    $stSql .= "             AND registro_evento_parcela.cod_registro = ultimo_registro_evento.cod_registro                               \n";
    $stSql .= "             AND registro_evento_parcela.timestamp = ultimo_registro_evento.timestamp                                     \n";
    $stSql .= "  INNER JOIN folhapagamento.evento_calculado                                                                                                        \n";
    $stSql .= "          ON ultimo_registro_evento.cod_registro = evento_calculado.cod_registro                                                                    \n";
    $stSql .= "         AND ultimo_registro_evento.cod_evento = evento_calculado.cod_evento                                                                        \n";
    $stSql .= "         AND ultimo_registro_evento.timestamp = evento_calculado.timestamp_registro                                                                 \n";
    $stSql .= "  INNER JOIN folhapagamento.evento                                                                                                                  \n";
    $stSql .= "          ON evento.cod_evento = evento_calculado.cod_evento                                                                                        \n";
    $stSql .=   $stFiltro;
    $stSql .=   $stOrdem;

    return $stSql;
}

function montaRecuperarContratosFichaFinanceiraComplementar()
{
    $stSql  = "SELECT to_real(evento_complementar_calculado.valor) as valor                                                                                                 \n";
    $stSql .= "     , to_real(evento_complementar_calculado.quantidade) as quantidade                                                                                       \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_complementar.cod_contrato) as matricula         \n";
    $stSql .= "     , registro_evento_complementar.cod_contrato                                                                                                             \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                       \n";
    $stSql .= "     , configuracao_evento.descricao as descricao                                                                                                                                   \n";
    $stSql .= "     , registro_evento_complementar.cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                                \n";
    $stSql .= " FROM folhapagamento.registro_evento_complementar                                                                                  \n";
    $stSql .= "     , folhapagamento.evento_complementar_calculado                                                                                \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                          \n";
    $stSql .= "     , pessoal.servidor                                                                                                            \n";
    $stSql .= "     , folhapagamento.evento                                                                                                       \n";
    $stSql .= "     , folhapagamento.configuracao_evento                                                                                                                    \n";
    $stSql .= "WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                                                                 \n";
    $stSql .= "  AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro                                                              \n";
    $stSql .= "  AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                                                                     \n";
    $stSql .= "  AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                                                         \n";
    $stSql .= "  AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                    \n";
    $stSql .= "  AND registro_evento_complementar.cod_configuracao = configuracao_evento.cod_configuracao                                                                   \n";
    $stSql .= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                        \n";
    $stSql .= "  AND evento_complementar_calculado.cod_evento = evento.cod_evento                                                                                           \n";

    return $stSql;
}

function montaRecuperarContratosFichaFinanceiraComplementarComPensionista($stFiltro,$stOrdem)
{
    $stSql  = "SELECT to_real(evento_complementar_calculado.valor) as valor                                                                                                 \n";
    $stSql .= "     , to_real(evento_complementar_calculado.quantidade) as quantidade                                                                                       \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_complementar.cod_contrato) as matricula         \n";
    $stSql .= "     , registro_evento_complementar.cod_contrato                                                                                                             \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                       \n";
    $stSql .= "     , (CASE WHEN evento_complementar_calculado.cod_configuracao=2 AND evento_complementar_calculado.desdobramento='F' THEN 'Férias'
                            WHEN evento_complementar_calculado.cod_configuracao=2 AND evento_complementar_calculado.desdobramento='D' THEN 'Adiant.Férias'
                WHEN evento_complementar_calculado.cod_configuracao=2 AND evento_complementar_calculado.desdobramento='A' THEN 'Abono Férias'
                WHEN evento_complementar_calculado.cod_configuracao=3 AND evento_complementar_calculado.desdobramento='D' THEN 'Décimo'
                WHEN evento_complementar_calculado.cod_configuracao=3 AND evento_complementar_calculado.desdobramento='A' THEN 'Adiant.Décimo'
                WHEN evento_complementar_calculado.cod_configuracao=1 AND evento_complementar_calculado.desdobramento=NULL THEN ''
                  END) as descricao\n";
    $stSql .= "     , registro_evento_complementar.cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                                \n";
    $stSql .= " FROM folhapagamento.registro_evento_complementar                                                                                  \n";
    $stSql .= "     , folhapagamento.evento_complementar_calculado                                                                                \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                          \n";
    $stSql .= "     , pessoal.servidor                                                                                                            \n";
    $stSql .= "     , folhapagamento.evento                                                                                                       \n";
    $stSql .= "     , folhapagamento.configuracao_evento                                                                                                                    \n";
    $stSql .= "WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                                                                 \n";
    $stSql .= "  AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro                                                              \n";
    $stSql .= "  AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                                                                     \n";
    $stSql .= "  AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                                                         \n";
    $stSql .= "  AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                    \n";
    $stSql .= "  AND registro_evento_complementar.cod_configuracao = configuracao_evento.cod_configuracao                                                                   \n";
    $stSql .= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                        \n";
    $stSql .= "  AND evento_complementar_calculado.cod_evento = evento.cod_evento                                                                                           \n";
    $stSql .= $stFiltro;
    $stSql .= " UNION                                                                                                                                                        \n";
    $stSql .= " SELECT to_real(evento_complementar_calculado.valor) as valor                                                                                                \n";
    $stSql .= "      , to_real(evento_complementar_calculado.quantidade) as quantidade                                                                                      \n";
    $stSql .= "      , (select registro from pessoal.contrato where cod_contrato = registro_evento_complementar.cod_contrato) as matricula                                  \n";
    $stSql .= "      , registro_evento_complementar.cod_contrato                                                                                                            \n";
    $stSql .= "      , pensionista.numcgm                                                                                                                                   \n";
    $stSql .= "     , (CASE WHEN evento_complementar_calculado.cod_configuracao=2 AND evento_complementar_calculado.desdobramento='F' THEN 'Férias'
                            WHEN evento_complementar_calculado.cod_configuracao=2 AND evento_complementar_calculado.desdobramento='D' THEN 'Adiant.Férias'
                WHEN evento_complementar_calculado.cod_configuracao=2 AND evento_complementar_calculado.desdobramento='A' THEN 'Abono Férias'
                WHEN evento_complementar_calculado.cod_configuracao=3 AND evento_complementar_calculado.desdobramento='D' THEN 'Décimo'
                WHEN evento_complementar_calculado.cod_configuracao=3 AND evento_complementar_calculado.desdobramento='A' THEN 'Adiant.Décimo'
                WHEN evento_complementar_calculado.cod_configuracao=1 AND evento_complementar_calculado.desdobramento=NULL THEN ''
                  END) as descricao\n";
    $stSql .= "      , registro_evento_complementar.cod_periodo_movimentacao                                                                                                \n";
    $stSql .= "      , (select nom_cgm from sw_cgm where numcgm = pensionista.numcgm) as nom_cgm                                                                            \n";
    $stSql .= "  FROM folhapagamento.registro_evento_complementar                                                                                                           \n";
    $stSql .= " INNER JOIN folhapagamento.evento_complementar_calculado                                                                                                     \n";
    $stSql .= "         ON registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro                                                           \n";
    $stSql .= "        AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro                                                        \n";
    $stSql .= "        AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento                                                               \n";
    $stSql .= "        AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                                                   \n";
    $stSql .= " INNER JOIN pessoal.contrato_pensionista                                                                                                                     \n";
    $stSql .= "         ON registro_evento_complementar.cod_contrato = contrato_pensionista.cod_contrato                                                                    \n";
    $stSql .= " INNER JOIN pessoal.pensionista                                                                                                                              \n";
    $stSql .= "         ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                               \n";
    $stSql .= "        AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                                                                     \n";
    $stSql .= " INNER JOIN folhapagamento.evento                                                                                                                            \n";
    $stSql .= "         ON evento_complementar_calculado.cod_evento = evento.cod_evento                                                                                     \n";
    $stSql .= " INNER JOIN folhapagamento.configuracao_evento                                                                                                               \n";
    $stSql .= "         ON registro_evento_complementar.cod_configuracao = configuracao_evento.cod_configuracao                                                             \n";
    $stSql .= $stFiltro;
    $stSql .= $stOrdem;

    return $stSql;
}

function montaRecuperarContratosFichaFinanceiraFerias()
{
    $stSql  = "SELECT to_real(evento_ferias_calculado.valor) as valor                                                                                                 \n";
    $stSql .= "     , to_real(evento_ferias_calculado.quantidade) as quantidade                                                                                       \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_ferias.cod_contrato) as matricula         \n";
    $stSql .= "     , registro_evento_ferias.cod_contrato                                                                                                             \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                 \n";
    $stSql .= "     , ( case when registro_evento_ferias.desdobramento = 'A'  then 'Abono'                                                                            \n";
    $stSql .= "              when registro_evento_ferias.desdobramento = 'F'  then 'Férias'                                                                           \n";
    $stSql .= "              when registro_evento_ferias.desdobramento = 'D'  then 'Adiantamento'                                                                     \n";
    $stSql .= "        end ) as descricao                                                                                                                             \n";
    $stSql .= "     , registro_evento_ferias.cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                          \n";
    $stSql .= " FROM folhapagamento.registro_evento_ferias                                                                                  \n";
    $stSql .= "     , folhapagamento.evento_ferias_calculado                                                                                \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                    \n";
    $stSql .= "     , pessoal.servidor                                                                                                      \n";
    $stSql .= "     , folhapagamento.evento                                                                                                 \n";
    $stSql .= "WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro                                                                       \n";
    $stSql .= "  AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro                                                                    \n";
    $stSql .= "  AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento                                                                           \n";
    $stSql .= "  AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento                                                                     \n";
    $stSql .= "  AND registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                    \n";
    $stSql .= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                  \n";
    $stSql .= "  AND evento_ferias_calculado.cod_evento = evento.cod_evento                                                                                           \n";

    return $stSql;
}

function montaRecuperarContratosFichaFinanceiraFeriasComPensionista($stFiltro,$stOrdem)
{
    $stSql  = "SELECT to_real(evento_ferias_calculado.valor) as valor                                                                                                 \n";
    $stSql .= "     , to_real(evento_ferias_calculado.quantidade) as quantidade                                                                                       \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_ferias.cod_contrato) as matricula         \n";
    $stSql .= "     , registro_evento_ferias.cod_contrato                                                                                                             \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                 \n";
    $stSql .= "     , ( case when registro_evento_ferias.desdobramento = 'A'  then 'Abono'                                                                            \n";
    $stSql .= "              when registro_evento_ferias.desdobramento = 'F'  then 'Férias'                                                                           \n";
    $stSql .= "              when registro_evento_ferias.desdobramento = 'D'  then 'Adiantamento'                                                                     \n";
    $stSql .= "        end ) as descricao                                                                                                                             \n";
    $stSql .= "     , registro_evento_ferias.cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                          \n";
    $stSql .= " FROM folhapagamento.registro_evento_ferias                                                                                  \n";
    $stSql .= "     , folhapagamento.evento_ferias_calculado                                                                                \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                    \n";
    $stSql .= "     , pessoal.servidor                                                                                                      \n";
    $stSql .= "     , folhapagamento.evento                                                                                                 \n";
    $stSql .= "WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro                                                                       \n";
    $stSql .= "  AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro                                                                    \n";
    $stSql .= "  AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento                                                                           \n";
    $stSql .= "  AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento                                                                     \n";
    $stSql .= "  AND registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                    \n";
    $stSql .= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                  \n";
    $stSql .= "  AND evento_ferias_calculado.cod_evento = evento.cod_evento                                                                                           \n";
    $stSql .= $stFiltro;
    //adicionada união para buscar pensionista
    $stSql .= " UNION                                                                                                                                                  \n";
    $stSql .= "  SELECT to_real(evento_ferias_calculado.valor) as valor                                                                                               \n";
    $stSql .= "      , to_real(evento_ferias_calculado.quantidade) as quantidade                                                                                      \n";
    $stSql .= "      , (select registro from pessoal.contrato where cod_contrato = registro_evento_ferias.cod_contrato) as matricula                                  \n";
    $stSql .= "      , registro_evento_ferias.cod_contrato                                                                                                            \n";
    $stSql .= "      , pensionista.numcgm                                                                                                                             \n";
    $stSql .= "      , ( case when registro_evento_ferias.desdobramento = 'A'  then 'Abono'                                                                           \n";
    $stSql .= "               when registro_evento_ferias.desdobramento = 'F'  then 'Férias'                                                                          \n";
    $stSql .= "               when registro_evento_ferias.desdobramento = 'D'  then 'Adiantamento'                                                                    \n";
    $stSql .= "         end ) as descricao                                                                                                                            \n";
    $stSql .= "      , registro_evento_ferias.cod_periodo_movimentacao                                                                                                \n";
    $stSql .= "      , (select nom_cgm from sw_cgm where numcgm = pensionista.numcgm) as nom_cgm                                                                      \n";
    $stSql .= "  FROM folhapagamento.registro_evento_ferias                                                                                                           \n";
    $stSql .= " INNER JOIN folhapagamento.evento_ferias_calculado                                                                                                     \n";
    $stSql .= "         ON registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro                                                                 \n";
    $stSql .= "        AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro                                                              \n";
    $stSql .= "        AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento                                                                     \n";
    $stSql .= "        AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento                                                               \n";
    $stSql .= " INNER JOIN pessoal.contrato_pensionista                                                                                                               \n";
    $stSql .= "         ON registro_evento_ferias.cod_contrato = contrato_pensionista.cod_contrato                                                                    \n";
    $stSql .= " INNER JOIN pessoal.pensionista                                                                                                                        \n";
    $stSql .= "         ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                         \n";
    $stSql .= "        AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                                                               \n";
    $stSql .= " INNER JOIN folhapagamento.evento                                                                                                                      \n";
    $stSql .= "         ON evento_ferias_calculado.cod_evento = evento.cod_evento                                                                                     \n";
    $stSql .= $stFiltro;
    $stSql .= $stOrdem;

    return $stSql;
}

function montaRecuperarContratosFichaFinanceiraDecimo()
{
    $stSql  = "SELECT to_real(evento_decimo_calculado.valor) as valor                                                                                                 \n";
    $stSql .= "     , to_real(evento_decimo_calculado.quantidade) as quantidade                                                                                       \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_decimo.cod_contrato) as matricula         \n";
    $stSql .= "     , registro_evento_decimo.cod_contrato                                                                                                             \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                 \n";
    $stSql .= "     , ( case when registro_evento_decimo.desdobramento = 'A'  then 'Adiantamento'                                                                     \n";
    $stSql .= "              when registro_evento_decimo.desdobramento = 'D'  then '13º Salário'                                                                      \n";
    $stSql .= "              when registro_evento_decimo.desdobramento = 'C'  then 'Complemento 13º Salário'                                                          \n";
    $stSql .= "        end ) as descricao                                                                                                                             \n";
    $stSql .= "     , registro_evento_decimo.cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                          \n";
    $stSql .= " FROM folhapagamento.registro_evento_decimo                                                                                  \n";
    $stSql .= "     , folhapagamento.evento_decimo_calculado                                                                                \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                    \n";
    $stSql .= "     , pessoal.servidor                                                                                                      \n";
    $stSql .= "     , folhapagamento.evento                                                                                                 \n";
    $stSql .= "WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro                                                                       \n";
    $stSql .= "  AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro                                                                    \n";
    $stSql .= "  AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento                                                                           \n";
    $stSql .= "  AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento                                                                     \n";
    $stSql .= "  AND registro_evento_decimo.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                    \n";
    $stSql .= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                  \n";
    $stSql .= "  AND evento_decimo_calculado.cod_evento = evento.cod_evento                                                                                           \n";

    return $stSql;
}

function montaRecuperarContratosFichaFinanceiraDecimoComPensionista($stFiltro,$stOrdem)
{
    $stSql  = "SELECT to_real(evento_decimo_calculado.valor) as valor                                                                                                 \n";
    $stSql .= "     , to_real(evento_decimo_calculado.quantidade) as quantidade                                                                                       \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_decimo.cod_contrato) as matricula         \n";
    $stSql .= "     , registro_evento_decimo.cod_contrato                                                                                                             \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                 \n";
    $stSql .= "     , ( case when registro_evento_decimo.desdobramento = 'A'  then 'Adiantamento'                                                                     \n";
    $stSql .= "              when registro_evento_decimo.desdobramento = 'D'  then '13º Salário'                                                                      \n";
    $stSql .= "              when registro_evento_decimo.desdobramento = 'C'  then 'Complemento 13º Salário'                                                          \n";
    $stSql .= "        end ) as descricao                                                                                                                             \n";
    $stSql .= "     , registro_evento_decimo.cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                          \n";
    $stSql .= " FROM folhapagamento.registro_evento_decimo                                                                                  \n";
    $stSql .= "     , folhapagamento.evento_decimo_calculado                                                                                \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                    \n";
    $stSql .= "     , pessoal.servidor                                                                                                      \n";
    $stSql .= "     , folhapagamento.evento                                                                                                 \n";
    $stSql .= "WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro                                                                       \n";
    $stSql .= "  AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro                                                                    \n";
    $stSql .= "  AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento                                                                           \n";
    $stSql .= "  AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento                                                                     \n";
    $stSql .= "  AND registro_evento_decimo.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                    \n";
    $stSql .= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                  \n";
    $stSql .= "  AND evento_decimo_calculado.cod_evento = evento.cod_evento                                                                                           \n";
    $stSql .= $stFiltro;
    //adicionada união para buscar pensionista
    $stSql .= " UNION                                                                                                                                                  \n";
    $stSql .= "   SELECT to_real(evento_decimo_calculado.valor) as valor                                                                                              \n";
    $stSql .= "      , to_real(evento_decimo_calculado.quantidade) as quantidade                                                                                      \n";
    $stSql .= "      , (select registro from pessoal.contrato where cod_contrato = registro_evento_decimo.cod_contrato) as matricula                                  \n";
    $stSql .= "      , registro_evento_decimo.cod_contrato                                                                                                            \n";
    $stSql .= "      , pensionista.numcgm                                                                                                                                \n";
    $stSql .= "      , ( case when registro_evento_decimo.desdobramento = 'A'  then 'Adiantamento'                                                                    \n";
    $stSql .= "               when registro_evento_decimo.desdobramento = 'D'  then '13º Salário'                                                                     \n";
    $stSql .= "               when registro_evento_decimo.desdobramento = 'C'  then 'Complemento 13º Salário'                                                         \n";
    $stSql .= "         end ) as descricao                                                                                                                            \n";
    $stSql .= "      , registro_evento_decimo.cod_periodo_movimentacao                                                                                                \n";
    $stSql .= "      , (select nom_cgm from sw_cgm where numcgm = pensionista.numcgm) as nom_cgm                                                                         \n";
    $stSql .= "  FROM folhapagamento.registro_evento_decimo                                                                                                           \n";
    $stSql .= " INNER JOIN folhapagamento.evento_decimo_calculado                                                                                                     \n";
    $stSql .= "         ON registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro                                                                 \n";
    $stSql .= "        AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento                                                               \n";
    $stSql .= " INNER JOIN pessoal.contrato_pensionista                                                                                                               \n";
    $stSql .= "         ON registro_evento_decimo.cod_contrato = pessoal.contrato_pensionista.cod_contrato                                                            \n";
    $stSql .= " INNER JOIN pessoal.pensionista                                                                                                                        \n";
    $stSql .= "         ON pensionista.cod_pensionista = contrato_pensionista.cod_pensionista                                                                         \n";
    $stSql .= "        AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente                                                               \n";
    $stSql .= " INNER JOIN folhapagamento.evento                                                                                                                      \n";
    $stSql .= "         ON evento_decimo_calculado.cod_evento = evento.cod_evento                                                                                     \n";
    $stSql .= $stFiltro;
    $stSql .= $stOrdem;

    return $stSql;
}

function montaRecuperarContratosFichaFinanceiraRescisao()
{
    $stSql  = "SELECT to_real(evento_rescisao_calculado.valor) as valor                                                                                                 \n";
    $stSql .= "     , to_real(evento_rescisao_calculado.quantidade) as quantidade                                                                                       \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_rescisao.cod_contrato) as matricula         \n";
    $stSql .= "     , registro_evento_rescisao.cod_contrato                                                                                                             \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                   \n";
    $stSql .= "     , ( case when registro_evento_rescisao.desdobramento = 'S'  then 'Saldo Salário'                                                                       \n";
    $stSql .= "              when registro_evento_rescisao.desdobramento = 'A'  then 'Aviso Prévio Indenizado'                                                                        \n";
    $stSql .= "              when registro_evento_rescisao.desdobramento = 'V'  then 'Férias Vencidas'                                                            \n";
    $stSql .= "              when registro_evento_rescisao.desdobramento = 'P'  then 'Férias Proporcionais'                                                            \n";
    $stSql .= "              when registro_evento_rescisao.desdobramento = 'D'  then '13º Salário'                                                            \n";
    $stSql .= "        end ) as descricao                                                                                                                               \n";
    $stSql .= "     , registro_evento_rescisao.cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                            \n";
    $stSql .= " FROM folhapagamento.registro_evento_rescisao                                                                                  \n";
    $stSql .= "     , folhapagamento.evento_rescisao_calculado                                                                                \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                      \n";
    $stSql .= "     , pessoal.servidor                                                                                                        \n";
    $stSql .= "     , folhapagamento.evento                                                                                                   \n";
    $stSql .= "WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro                                                                     \n";
    $stSql .= "  AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro                                                                  \n";
    $stSql .= "  AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento                                                                         \n";
    $stSql .= "  AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento                                                                   \n";
    $stSql .= "  AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                    \n";
    $stSql .= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                    \n";
    $stSql .= "  AND evento_rescisao_calculado.cod_evento = evento.cod_evento                                                                                           \n";

    return $stSql;
}

function montaRecuperarContratosFichaFinanceiraRescisaoComPensionista($stFiltro,$stOrdem)
{
    $stSql  = "SELECT to_real(evento_rescisao_calculado.valor) as valor                                                                                                 \n";
    $stSql .= "     , to_real(evento_rescisao_calculado.quantidade) as quantidade                                                                                       \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_rescisao.cod_contrato) as matricula         \n";
    $stSql .= "     , registro_evento_rescisao.cod_contrato                                                                                                             \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                   \n";
    $stSql .= "     , ( case when registro_evento_rescisao.desdobramento = 'S'  then 'Saldo Salário'                                                                       \n";
    $stSql .= "              when registro_evento_rescisao.desdobramento = 'A'  then 'Aviso Prévio Indenizado'                                                                        \n";
    $stSql .= "              when registro_evento_rescisao.desdobramento = 'V'  then 'Férias Vencidas'                                                            \n";
    $stSql .= "              when registro_evento_rescisao.desdobramento = 'P'  then 'Férias Proporcionais'                                                            \n";
    $stSql .= "              when registro_evento_rescisao.desdobramento = 'D'  then '13º Salário'                                                            \n";
    $stSql .= "        end ) as descricao                                                                                                                               \n";
    $stSql .= "     , registro_evento_rescisao.cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                            \n";
    $stSql .= " FROM folhapagamento.registro_evento_rescisao                                                                                  \n";
    $stSql .= "     , folhapagamento.evento_rescisao_calculado                                                                                \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                      \n";
    $stSql .= "     , pessoal.servidor                                                                                                        \n";
    $stSql .= "     , folhapagamento.evento                                                                                                   \n";
    $stSql .= "WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro                                                                     \n";
    $stSql .= "  AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro                                                                  \n";
    $stSql .= "  AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento                                                                         \n";
    $stSql .= "  AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento                                                                   \n";
    $stSql .= "  AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                    \n";
    $stSql .= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                    \n";
    $stSql .= "  AND evento_rescisao_calculado.cod_evento = evento.cod_evento                                                                                           \n";
    $stSql .= $stFiltro;
    //adicionada união para buscar pensionista
    $stSql .= " UNION                                                                                                                                                    \n";
    $stSql .= "  SELECT to_real(evento_rescisao_calculado.valor) as valor                                                                                               \n";
    $stSql .= "      , to_real(evento_rescisao_calculado.quantidade) as quantidade                                                                                      \n";
    $stSql .= "      , (select registro from pessoal.contrato where cod_contrato = registro_evento_rescisao.cod_contrato) as matricula                                  \n";
    $stSql .= "      , registro_evento_rescisao.cod_contrato                                                                                                            \n";
    $stSql .= "      , pensionista.numcgm                                                                                                                               \n";
    $stSql .= "      , ( case when registro_evento_rescisao.desdobramento = 'S'  then 'Saldo Salário'                                                                   \n";
    $stSql .= "               when registro_evento_rescisao.desdobramento = 'A'  then 'Aviso Prévio Indenizado'                                                         \n";
    $stSql .= "               when registro_evento_rescisao.desdobramento = 'V'  then 'Férias Vencidas'                                                                 \n";
    $stSql .= "               when registro_evento_rescisao.desdobramento = 'P'  then 'Férias Proporcionais'                                                            \n";
    $stSql .= "               when registro_evento_rescisao.desdobramento = 'D'  then '13º Salário'                                                                     \n";
    $stSql .= "         end ) as descricao                                                                                                                              \n";
    $stSql .= "      , registro_evento_rescisao.cod_periodo_movimentacao                                                                                                \n";
    $stSql .= "      , (select nom_cgm from sw_cgm where numcgm = pensionista.numcgm) as nom_cgm                                                                        \n";
    $stSql .= "  FROM folhapagamento.registro_evento_rescisao                                                                                                           \n";
    $stSql .= " INNER JOIN folhapagamento.evento_rescisao_calculado                                                                                                     \n";
    $stSql .= "         ON registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro                                                               \n";
    $stSql .= "        AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro                                                            \n";
    $stSql .= "        AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento                                                                   \n";
    $stSql .= "        AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento                                                             \n";
    $stSql .= " INNER JOIN pessoal.contrato_pensionista                                                                                                                 \n";
    $stSql .= "         ON registro_evento_rescisao.cod_contrato = contrato_pensionista.cod_contrato                                                                    \n";
    $stSql .= " INNER JOIN pessoal.pensionista                                                                                                                          \n";
    $stSql .= "         ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                           \n";
    $stSql .= "        AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                                                                 \n";
    $stSql .= " INNER JOIN folhapagamento.evento                                                                                                                        \n";
    $stSql .= "         ON evento_rescisao_calculado.cod_evento = evento.cod_evento                                                                                     \n";
    $stSql .= $stFiltro;
    $stSql .= $stOrdem;

    return $stSql;
}

function recuperaContratosFichaFinanceiraRescisao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarContratosFichaFinanceiraRescisao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaContratosFichaFinanceiraRescisaoComPensionista(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarContratosFichaFinanceiraRescisaoComPensionista($stFiltro,$stOrdem);
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaContratosFichaFinanceiraDecimo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarContratosFichaFinanceiraDecimo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaContratosFichaFinanceiraDecimoComPensionista(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarContratosFichaFinanceiraDecimoComPensionista($stFiltro,$stOrdem);
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaContratosFichaFinanceiraFerias(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarContratosFichaFinanceiraFerias().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaContratosFichaFinanceiraFeriasComPensionista(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarContratosFichaFinanceiraFeriasComPensionista($stFiltro,$stOrdem);
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaContratosFichaFinanceiraComplementar(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarContratosFichaFinanceiraComplementar().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaContratosFichaFinanceiraComplementarComPensionista(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarContratosFichaFinanceiraComplementarComPensionista($stFiltro,$stOrdem);
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaCodigoEventoFichaFinanceira(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaCodigoEventoFichaFinanceira().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCodigoEventoFichaFinanceira()
{

    $stSql  = "select cod_evento          \n";
    $stSql .= "from folhapagamento.evento \n";

    return $stSql;
}

function recuperaValoresAcumuladosCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaValoresAcumuladosCalculo", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaValoresAcumuladosCalculo()
{
    $stSql = "select * from recuperaValoresAcumuladosCalculo(
    ".$this->getDado("cod_contrato").",
    ".$this->getDado("cod_periodo_movimentacao").",
    ".$this->getDado("numcgm").",
    '".$this->getDado("natureza")."',
    '".Sessao::getEntidade()."'
    )
    ";

    return $stSql;
}

function recuperaRotuloValoresAcumuladosCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaRotuloValoresAcumuladosCalculo", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaRotuloValoresAcumuladosCalculo()
{
    $stSql = "select recuperaRotuloValoresAcumuladosCalculo(
    ".$this->getDado("cod_contrato").",
    ".$this->getDado("cod_periodo_movimentacao").",
    ".$this->getDado("numcgm").",
    '".$this->getDado("natureza")."',
    '".Sessao::getEntidade()."'
    ) as rotulo";

    return $stSql;
}


function recuperaValoresAcumuladosCalculoSalarioFamilia(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaValoresAcumuladosCalculoSalarioFamilia", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaValoresAcumuladosCalculoSalarioFamilia()
{
    $stSql = "select * from recuperaValoresAcumuladosCalculoSalarioFamilia(
    ".$this->getDado("cod_contrato").",
    ".$this->getDado("cod_periodo_movimentacao").",
    ".$this->getDado("numcgm").",
    '".$this->getDado("natureza")."',
    '".Sessao::getEntidade()."'
    )";

    return $stSql;
}

function recuperaRotuloValoresAcumuladosCalculoSalarioFamilia(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaRotuloValoresAcumuladosCalculoSalarioFamilia", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaRotuloValoresAcumuladosCalculoSalarioFamilia()
{
    $stSql = "select recuperaRotuloValoresAcumuladosCalculoSalarioFamilia(
    ".$this->getDado("cod_contrato").",
    ".$this->getDado("cod_periodo_movimentacao").",
    ".$this->getDado("numcgm").",
    '".$this->getDado("natureza")."',
    '".Sessao::getEntidade()."'
    ) as rotulo";

    return $stSql;
}

function recuperaEventosCalculadosAutorizacaoEmpenho(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $stOrdem = ($stOrdem != "") ? $stOrdem : " ORDER BY descricao";
    $obErro = $this->executaRecupera("montaRecuperaEventosCalculadosAutorizacaoEmpenho",$rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

    return $obErro;
}

function montaRecuperaEventosCalculadosAutorizacaoEmpenho()
{
    $stSql  = "    SELECT evento.codigo                                                                                             \n";
    $stSql .= "         , evento.descricao                                                                                          \n";
    $stSql .= "         , evento.cod_evento                                                                                         \n";
    $stSql .= "      FROM folhapagamento.evento                                                            \n";
    $stSql .= "     WHERE EXISTS (    SELECT 1                                                                                      \n";
    $stSql .= "                         FROM folhapagamento.evento_calculado                               \n";
    $stSql .= "                   INNER JOIN folhapagamento.registro_evento_periodo                        \n";
    $stSql .= "                           ON registro_evento_periodo.cod_registro = evento_calculado.cod_registro                   \n";
    $stSql .= "                          AND registro_evento_periodo.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."\n";
    if (trim($this->getDado("cod_orgao")) != "") {
        $stSql .= "                          AND EXISTS (     SELECT contrato_servidor_orgao.*                                                              \n";
        $stSql .= "                                             FROM pessoal.contrato_servidor_orgao                               \n";
        $stSql .= "                                       INNER JOIN (  SELECT cod_contrato                                                                 \n";
        $stSql .= "                                                          , max(timestamp) as timestamp                                                  \n";
        $stSql .= "                                                       FROM pessoal.contrato_servidor_orgao                     \n";
        $stSql .= "                                                      WHERE contrato_servidor_orgao.timestamp::date <= '".$this->getDado("vigencia")."'  \n";
        $stSql .= "                                                   GROUP BY cod_contrato) as max_contrato_servidor_orgao                                 \n";
        $stSql .= "                                               ON contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato        \n";
        $stSql .= "                                              AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp              \n";
        $stSql .= "                                              AND contrato_servidor_orgao.cod_orgao = ".$this->getDado("cod_orgao")."                    \n";
        $stSql .= "                                            WHERE contrato_servidor_orgao.cod_contrato = registro_evento_periodo.cod_contrato)           \n";
    }
    if (trim($this->getDado("cod_local")) != "") {
        $stSql .= "                          AND EXISTS (     SELECT contrato_servidor_local.*                                                              \n";
        $stSql .= "                                             FROM pessoal.contrato_servidor_local                               \n";
        $stSql .= "                                       INNER JOIN (  SELECT cod_contrato                                                                 \n";
        $stSql .= "                                                          , max(timestamp) as timestamp                                                  \n";
        $stSql .= "                                                       FROM pessoal.contrato_servidor_local                     \n";
        $stSql .= "                                                   GROUP BY cod_contrato) as max_contrato_servidor_local                                 \n";
        $stSql .= "                                               ON contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato        \n";
        $stSql .= "                                              AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp              \n";
        $stSql .= "                                              AND contrato_servidor_local.cod_local = ".$this->getDado("cod_local")."                    \n";
        $stSql .= "                                            WHERE contrato_servidor_local.cod_contrato = registro_evento_periodo.cod_contrato)           \n";
    }
    $stSql .= "                        WHERE evento_calculado.cod_evento = evento.cod_evento                                        \n";
    $stSql .= "                        LIMIT 1)                                                                                     \n";

    return $stSql;
}

function deletarEventoCalculado($boTransacao="")
{
    return $this->executaRecupera("montaDeletarEventoCalculado", $rsRecordSet, "", "", $boTransacao);
}

function montaDeletarEventoCalculado()
{
    $stSql  = "SELECT criarBufferTexto('stEntidade','".Sessao::getEntidade()."');       \n";
    $stSql .= "SELECT criarBufferTexto('stTipoFolha','S');                              \n";
    $stSql .= "SELECT deletarEventoCalculado(".$this->getDado("cod_registro")."    \n";
    $stSql .= "                             ,".$this->getDado("cod_evento")."      \n";
    $stSql .= "                            ,'".$this->getDado("desdobramento")."'  \n";
    $stSql .= "                            ,'".$this->getDado("timestamp")."');    \n";

    return $stSql;
}

}
