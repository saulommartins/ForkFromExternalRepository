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
    * Classe de mapeamento da tabela folhapagamento.evento_rescisao_calculado
    * Data de Criação: 16/10/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.54

    $Id: TFolhaPagamentoEventoRescisaoCalculado.class.php 65899 2016-06-27 17:35:49Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.evento_rescisao_calculado
  * Data de Criação: 16/10/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoEventoRescisaoCalculado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoEventoRescisaoCalculado()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.evento_rescisao_calculado");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_evento,cod_registro,desdobramento,timestamp');

    $this->AddCampo('cod_evento'        ,'integer'      ,true  ,''      ,true,'TFolhaPagamentoUltimoRegistroEventoRescisao');
    $this->AddCampo('cod_registro'      ,'integer'      ,true  ,''      ,true,'TFolhaPagamentoUltimoRegistroEventoRescisao');
    $this->AddCampo('desdobramento'     ,'char'         ,true  ,'1'     ,true,'TFolhaPagamentoUltimoRegistroEventoRescisao');
    $this->AddCampo('timestamp_registro','timestamp'    ,true  ,''      ,false,'TFolhaPagamentoUltimoRegistroEventoRescisao','timestamp');
    $this->AddCampo('valor'             ,'numeric'      ,true  ,'15,2'  ,false,false);
    $this->AddCampo('quantidade'        ,'numeric'      ,true  ,'15,2'  ,false,false);
    $this->AddCampo('timestamp'         ,'timestamp_now',true  ,''      ,true,false);

}

function recuperaContratosCalculados(&$rsRecordSet, $stFiltro = "", $stOrdem = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato ";
    $stSql = $this->montaRecuperaContratosCalculados().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosCalculados()
{
    $stSql  = "SELECT *                                                                                                     \n";
    $stSql .= "  FROM (SELECT registro_evento_rescisao.cod_contrato                                                         \n";
    $stSql .= "             , registro_evento_rescisao.cod_periodo_movimentacao                                             \n";
    $stSql .= "             , contrato.registro                                                                             \n";
    $stSql .= "             , sw_cgm.numcgm                                                                                 \n";
    $stSql .= "             , sw_cgm.nom_cgm                                                                                \n";
    $stSql .= "          FROM                                                                                               \n";
    $stSql .= "	       folhapagamento.ultimo_registro_evento_rescisao                                                       \n";
    $stSql .= "    INNER JOIN folhapagamento.registro_evento_rescisao                                                       \n";
    $stSql .= "            ON ultimo_registro_evento_rescisao.cod_registro = registro_evento_rescisao.cod_registro          \n";
    $stSql .= "           AND ultimo_registro_evento_rescisao.cod_evento = registro_evento_rescisao.cod_evento              \n";
    $stSql .= "           AND ultimo_registro_evento_rescisao.timestamp = registro_evento_rescisao.timestamp                \n";
    $stSql .= "    INNER JOIN folhapagamento.evento_rescisao_calculado                                                      \n";
    $stSql .= "            ON ultimo_registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro         \n";
    $stSql .= "           AND ultimo_registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento             \n";
    $stSql .= "           AND ultimo_registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro      \n";
    $stSql .= "     LEFT JOIN pessoal.servidor_contrato_servidor                                                            \n";
    $stSql .= "            ON registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato               \n";
    $stSql .= "     LEFT JOIN pessoal.servidor                                                                              \n";
    $stSql .= "            ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                               \n";
    $stSql .= "     LEFT JOIN pessoal.contrato_pensionista                                                                  \n";
    $stSql .= "	    ON registro_evento_rescisao.cod_contrato = contrato_pensionista.cod_contrato                            \n";
    $stSql .= "     LEFT JOIN pessoal.pensionista                                                                           \n";
    $stSql .= "            ON contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                  \n";
    $stSql .= "    INNER JOIN sw_cgm                                                                                        \n";
    $stSql .= "            ON servidor.numcgm = sw_cgm.numcgm                                                               \n";
    $stSql .= "            OR pensionista.numcgm = sw_cgm.numcgm                                                            \n";
    $stSql .= "    INNER JOIN pessoal.contrato                                                                              \n";
    $stSql .= "            ON registro_evento_rescisao.cod_contrato = contrato.cod_contrato                                 \n";
    $stSql .= "      GROUP BY registro_evento_rescisao.cod_contrato                                                         \n";
    $stSql .= "             , registro_evento_rescisao.cod_periodo_movimentacao                                             \n";
    $stSql .= "             , contrato.registro                                                                             \n";
    $stSql .= "             , sw_cgm.numcgm                                                                                 \n";
    $stSql .= "             , sw_cgm.nom_cgm) as contratos                                                                  \n";

    return $stSql;
}

function recuperaConsultaFichaFinanceira(&$rsRecordSet, $stFiltro = "", $stOrdem = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY evento.codigo ";
    $stSql = $this->montaRecuperaConsultaFichaFinanceira().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsultaFichaFinanceira()
{
    $stSql  = "SELECT evento_rescisao_calculado.*                                                                 \n";
    $stSql .= "     , getDesdobramentoRescisao(evento_rescisao_calculado.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto    \n";
    $stSql .= "     , evento.natureza                                                                             \n";
    $stSql .= "     , evento.codigo                                                                               \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                                         \n";
    $stSql .= "     , CASE evento.natureza                                                                        \n";
    $stSql .= "       WHEN 'P' THEN 'proventos'                                                                   \n";
    $stSql .= "       WHEN 'D' THEN 'descontos'                                                                   \n";
    $stSql .= "       WHEN 'B' THEN 'base'                                                                        \n";
    $stSql .= "       END as proventos_descontos                                                                  \n";
    $stSql .= "     , registro_evento_rescisao.cod_contrato                                                       \n";
    $stSql .= "  FROM folhapagamento.ultimo_registro_evento_rescisao                                              \n";
    $stSql .= "     , folhapagamento.registro_evento_rescisao                                                     \n";
    $stSql .= "     , folhapagamento.evento_rescisao_calculado                                                    \n";
    $stSql .= "     , folhapagamento.evento                                                                       \n";
    $stSql .= "     , folhapagamento.sequencia_calculo_evento 					                                  \n";
    $stSql .= "     , folhapagamento.sequencia_calculo 						                                      \n";
    $stSql .= " WHERE ultimo_registro_evento_rescisao.cod_registro = registro_evento_rescisao.cod_registro        \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.cod_evento   = registro_evento_rescisao.cod_evento          \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.timestamp    = registro_evento_rescisao.timestamp           \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.desdobramento= registro_evento_rescisao.desdobramento       \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro       \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.cod_evento   = evento_rescisao_calculado.cod_evento         \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.timestamp    = evento_rescisao_calculado.timestamp_registro \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.desdobramento= evento_rescisao_calculado.desdobramento      \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.cod_evento   = evento.cod_evento                            \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.cod_evento   = sequencia_calculo_evento.cod_evento          \n";
    $stSql .= "   AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia                    \n";

    return $stSql;
}

function recuperaEventoRescisaoCalculado(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY descricao ";
    $stSql  = $this->montaRecuperaEventoRescisaoCalculado().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventoRescisaoCalculado()
{
    $stSql  = "SELECT evento_rescisao_calculado.*                                                                      
                 , evento.descricao                                                                                 
                 , evento.codigo                                                                                    
                 , evento.natureza                                                                                  
                 , getDesdobramentoRescisao(evento_rescisao_calculado.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto         
                 , evento.descricao as nom_evento                                                                   
              FROM folhapagamento.evento_rescisao_calculado                                                         
                 INNER JOIN folhapagamento.registro_evento_rescisao
                    ON registro_evento_rescisao.cod_registro     = evento_rescisao_calculado.cod_registro               
                    AND registro_evento_rescisao.cod_evento       = evento_rescisao_calculado.cod_evento                 
                    AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento                 
                    AND registro_evento_rescisao.timestamp        = evento_rescisao_calculado.timestamp_registro         
                 INNER JOIN folhapagamento.ultimo_registro_evento_rescisao
                    ON registro_evento_rescisao.cod_registro     = ultimo_registro_evento_rescisao.cod_registro         
                    AND registro_evento_rescisao.cod_evento       = ultimo_registro_evento_rescisao.cod_evento           
                    AND registro_evento_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento           
                    AND registro_evento_rescisao.timestamp        = ultimo_registro_evento_rescisao.timestamp
                 INNER JOIN folhapagamento.evento
                    ON evento_rescisao_calculado.cod_evento = evento.cod_evento
             WHERE 1=1
             \n";

    return $stSql;
}

function recuperaEventoRescisaoCalculadoFichaFinanceira(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY descricao ";
    $stSql  = $this->montaRecuperaEventoRescisaoCalculadoFichaFinanceira().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventoRescisaoCalculadoFichaFinanceira()
{
    $stSql  = "SELECT evento_rescisao_calculado.*                                                               \n";
    $stSql .= "     , evento.descricao                                                                          \n";
    $stSql .= "     , evento.codigo                                                                             \n";
    $stSql .= "     , evento.natureza                                                                           \n";
    $stSql .= "     , sequencia_calculo.sequencia                                                        \n";
    $stSql .= "  FROM folhapagamento.evento_rescisao_calculado                                                  \n";
    $stSql .= "     , folhapagamento.registro_evento_rescisao                                                   \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_rescisao                                            \n";
    $stSql .= "     , folhapagamento.evento                                                                     \n";
    $stSql .= "     , folhapagamento.sequencia_calculo_evento                                                   \n";
    $stSql .= "     , folhapagamento.sequencia_calculo                                                          \n";
    $stSql .= " WHERE registro_evento_rescisao.cod_registro     = ultimo_registro_evento_rescisao.cod_registro  \n";
    $stSql .= "   AND registro_evento_rescisao.cod_evento       = ultimo_registro_evento_rescisao.cod_evento    \n";
    $stSql .= "   AND registro_evento_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento    \n";
    $stSql .= "   AND registro_evento_rescisao.timestamp        = ultimo_registro_evento_rescisao.timestamp     \n";
    $stSql .= "   AND registro_evento_rescisao.cod_registro     = evento_rescisao_calculado.cod_registro        \n";
    $stSql .= "   AND registro_evento_rescisao.cod_evento       = evento_rescisao_calculado.cod_evento          \n";
    $stSql .= "   AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento          \n";
    $stSql .= "   AND registro_evento_rescisao.timestamp        = evento_rescisao_calculado.timestamp_registro  \n";
    $stSql .= "   AND evento_rescisao_calculado.cod_evento = evento.cod_evento                                  \n";
    $stSql .= "   AND evento_rescisao_calculado.cod_evento = sequencia_calculo_evento.cod_evento                \n";
    $stSql .= "   AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia                  \n";

    return $stSql;
}

function recuperaEventosCalculadosRais(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaEventosCalculadosRais",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaEventosCalculadosRais()
{
    $stSql  = "SELECT sum(evento_rescisao_calculado.valor) as valor                                                      \n";
    $stSql .= "  FROM folhapagamento.registro_evento_rescisao                                                            \n";
    $stSql .= "     , folhapagamento.evento_rescisao_calculado                                                           \n";
    $stSql .= "     , folhapagamento.periodo_movimentacao                                                                \n";
    $stSql .= " WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro                     \n";
    $stSql .= "   AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento                         \n";
    $stSql .= "   AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento                   \n";
    $stSql .= "   AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro                  \n";
    $stSql .= "   AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao  \n";

    return $stSql;
}

function recuperaValoresAcumuladosCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaValoresAcumuladosCalculo", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaValoresAcumuladosCalculo()
{
    $stSql = "select * from recuperaValoresAcumuladosCalculoRescisao(
    ".$this->getDado("cod_contrato").",
    ".$this->getDado("cod_periodo_movimentacao").",
    ".$this->getDado("numcgm").",
    '".$this->getDado("natureza")."',
    '".Sessao::getEntidade()."'
    )";

    return $stSql;
}

function recuperaRotuloValoresAcumuladosCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaRotuloValoresAcumuladosCalculo", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaRotuloValoresAcumuladosCalculo()
{
    $stSql = "select recuperaRotuloValoresAcumuladosCalculoRescisao(
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
    $stSql .= "                         FROM folhapagamento.evento_rescisao_calculado                        \n";
    $stSql .= "                   INNER JOIN folhapagamento.registro_evento_rescisao                         \n";
    $stSql .= "                           ON registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro             \n";
    $stSql .= "                          AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento                 \n";
    $stSql .= "                          AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento           \n";
    $stSql .= "                          AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro          \n";
    $stSql .= "                          AND registro_evento_rescisao.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."\n";
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
        $stSql .= "                                            WHERE contrato_servidor_orgao.cod_contrato = registro_evento_rescisao.cod_contrato)           \n";
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
        $stSql .= "                                            WHERE contrato_servidor_local.cod_contrato = registro_evento_rescisao.cod_contrato)           \n";
    }
    $stSql .= "                        WHERE evento_rescisao_calculado.cod_evento = evento.cod_evento                                        \n";
    $stSql .= "                        LIMIT 1)                                                                                     \n";

    return $stSql;
}

}
?>
