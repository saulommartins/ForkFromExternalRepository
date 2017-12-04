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
    * Classe de mapeamento da tabela folhapagamento.registro_evento_complementar
    * Data de Criação: 20/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 31001 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-04-03 10:54:36 -0300 (Qui, 03 Abr 2008) $

    * Casos de uso: uc-04.05.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.registro_evento_complementar
  * Data de Criação: 20/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoRegistroEventoComplementar extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoRegistroEventoComplementar()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.registro_evento_complementar");

    $this->setCampoCod('cod_registro');
    $this->setComplementoChave('timestamp,cod_evento,cod_configuracao');

    $this->AddCampo('cod_registro'              ,'sequence' ,true,''        ,true,false);
    $this->AddCampo('timestamp'                 ,'timestamp_now',true,''    ,true,false);
    $this->AddCampo('cod_evento'                ,'integer'  ,true,''        ,true,"TFolhaPagamentoEvento");
    $this->AddCampo('cod_configuracao'          ,'integer'  ,true,''        ,true,"TFolhaPagamentoConfiguracaoEvento");
    $this->AddCampo('cod_contrato'              ,'integer'  ,true,''        ,false,"TFolhaPagamentoContratoServidorComplementar");
    $this->AddCampo('cod_complementar'          ,'integer'  ,true,''        ,false,"TFolhaPagamentoContratoServidorComplementar");
    $this->AddCampo('cod_periodo_movimentacao'  ,'integer'  ,true,''        ,false,"TFolhaPagamentoContratoServidorComplementar");
    $this->AddCampo('valor'                     ,'numeric'  ,true,'15,2'    ,false,false);
    $this->AddCampo('quantidade'                ,'numeric'  ,true,'15,2'    ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "   SELECT registro_evento_complementar.*                                                                                \n";
    $stSql .= "        , registro_evento_complementar_parcela.parcela                                                                  \n";
    $stSql .= "        , evento.cod_evento                                                                                             \n";
    $stSql .= "        , evento.codigo                                                                                                 \n";
    $stSql .= "        , trim(evento.descricao) as descricao                                                                           \n";
    $stSql .= "        , evento.natureza                                                                                               \n";
    $stSql .= "        , evento.tipo                                                                                                   \n";
    $stSql .= "        , evento.fixado                                                                                                 \n";
    $stSql .= "        , evento.limite_calculo                                                                                         \n";
    $stSql .= "        , evento.apresenta_parcela                                                                                      \n";
    $stSql .= "        , evento.observacao                                                                                             \n";
    $stSql .= "        , CASE WHEN evento.descricao_configuracao = 'Férias' THEN \n";
    $stSql .= "             CASE evento_complementar_calculado.desdobramento \n";
    $stSql .= "                 WHEN 'A' THEN 'Abono Férias'                                        \n";
    $stSql .= "                 WHEN 'F' THEN 'Férias no Mês'                                       \n";
    $stSql .= "                 WHEN 'D' THEN 'Adiant. Férias'                                      \n";
    $stSql .= "                 ELSE 'Férias' END                                                   \n";
    $stSql .= "          ELSE evento.descricao_configuracao END AS descricao_configuracao                                              \n";
    $stSql .= "        , evento_complementar_calculado.desdobramento                                                                                        \n";
    $stSql .= "        , evento.evento_sistema                                                                                         \n";
    $stSql .= "        , contrato.registro                                                                                             \n";
    $stSql .= "     FROM folhapagamento.registro_evento_complementar                                         \n";
    $stSql .= "LEFT JOIN folhapagamento.evento_complementar_calculado                                        \n";
    $stSql .= "       ON registro_evento_complementar.cod_registro     = evento_complementar_calculado.cod_registro                    \n";
    $stSql .= "      AND registro_evento_complementar.timestamp        = evento_complementar_calculado.timestamp_registro                       \n";
    $stSql .= "      AND registro_evento_complementar.cod_evento       = evento_complementar_calculado.cod_evento                      \n";
    $stSql .= "      AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao                \n";
    $stSql .= "LEFT JOIN folhapagamento.registro_evento_complementar_parcela                                 \n";
    $stSql .= "       ON registro_evento_complementar.cod_registro     = registro_evento_complementar_parcela.cod_registro             \n";
    $stSql .= "      AND registro_evento_complementar.timestamp        = registro_evento_complementar_parcela.timestamp                \n";
    $stSql .= "      AND registro_evento_complementar.cod_evento       = registro_evento_complementar_parcela.cod_evento               \n";
    $stSql .= "      AND registro_evento_complementar.cod_configuracao = registro_evento_complementar_parcela.cod_configuracao         \n";
    $stSql .= "LEFT JOIN pessoal.contrato                                                                    \n";
    $stSql .= "       ON registro_evento_complementar.cod_contrato     = contrato.cod_contrato                                         \n";
    $stSql .= "LEFT JOIN (SELECT evento.*                                                                                              \n";
    $stSql .= "                , evento_evento.observacao                                                                              \n";
    $stSql .= "                , configuracao_evento.descricao as descricao_configuracao                                               \n";
    $stSql .= "                , configuracao_evento.cod_configuracao                                                                  \n";
    $stSql .= "             FROM folhapagamento.evento                                                                                 \n";
    $stSql .= "                , folhapagamento.evento_evento                                                                          \n";
    $stSql .= "                , (  SELECT cod_evento                                                                                  \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                 \n";
    $stSql .= "                       FROM folhapagamento.evento_evento                                                                \n";
    $stSql .= "                    GROUP BY cod_evento) as max_evento_evento                                                           \n";
    $stSql .= "                , folhapagamento.evento_configuracao_evento                                                             \n";
    $stSql .= "                , folhapagamento.configuracao_evento                                                                    \n";
    $stSql .= "            WHERE evento.cod_evento                         = evento_evento.cod_evento                                  \n";
    $stSql .= "              AND evento_evento.cod_evento                  = max_evento_evento.cod_evento                              \n";
    $stSql .= "              AND evento_evento.timestamp                   = max_evento_evento.timestamp                               \n";
    $stSql .= "              AND evento_evento.cod_evento                  = evento_configuracao_evento.cod_evento                     \n";
    $stSql .= "              AND evento_evento.timestamp                   = evento_configuracao_evento.timestamp                      \n";
    $stSql .= "              AND evento_configuracao_evento.cod_configuracao = configuracao_evento.cod_configuracao) as evento         \n";
    $stSql .= "       ON registro_evento_complementar.cod_evento       = evento.cod_evento                                             \n";
    $stSql .= "      AND registro_evento_complementar.cod_configuracao = evento.cod_configuracao                                       \n";
    $stSql .= "        , folhapagamento.ultimo_registro_evento_complementar                                                            \n";
    $stSql .= "    WHERE registro_evento_complementar.timestamp        = ultimo_registro_evento_complementar.timestamp                 \n";
    $stSql .= "      AND registro_evento_complementar.cod_registro     = ultimo_registro_evento_complementar.cod_registro              \n";
    $stSql .= "      AND registro_evento_complementar.cod_evento       = ultimo_registro_evento_complementar.cod_evento                \n";
    $stSql .= "      AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao          \n";

    return $stSql;
}

function recuperaRegistroEventoComplementarExclusao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : "";
    $stSql  = $this->montaRecuperaRegistroEventoComplementarExclusao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRegistroEventoComplementarExclusao()
{
    $stSql .= "SELECT registro_evento_complementar.cod_registro                 \n";
    $stSql .= "     , evento_complementar_calculado.timestamp_registro          \n";
    $stSql .= "  FROM folhapagamento.registro_evento_complementar               \n";
    $stSql .= "     , folhapagamento.evento_complementar_calculado              \n";
    $stSql .= " WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro \n";
    $stSql .= "   AND registro_evento_complementar.cod_evento   = evento_complementar_calculado.cod_evento   \n";
    $stSql .= "   AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao \n";
    $stSql .= "   AND registro_evento_complementar.timestamp        = evento_complementar_calculado.timestamp_registro \n";

    return $stSql;
}

function recuperaContratosComRegistroDeEventoPorCgm(&$rsRecordSet, $stFiltro, $stOrdem, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato ";
    $stSql  = $this->montaRecuperaContratosComRegistroDeEventoPorCgm().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosComRegistroDeEventoPorCgm()
{
    $stSql .= "SELECT *                                                                                \n";
    $stSql .= "  FROM (SELECT registro                                                                 \n";
    $stSql .= "             , contrato.cod_contrato                                                    \n";
    $stSql .= "             , servidor.numcgm                                                          \n";
    $stSql .= "             , registro_evento_complementar.cod_complementar                            \n";
    $stSql .= "             , registro_evento_complementar.cod_periodo_movimentacao                    \n";
    $stSql .= "          FROM folhapagamento.evento_complementar_calculado                             \n";
    $stSql .= "             , folhapagamento.registro_evento_complementar                              \n";
    $stSql .= "             , folhapagamento.ultimo_registro_evento_complementar                       \n";
    $stSql .= "             , folhapagamento.contrato_servidor_complementar                            \n";
    $stSql .= "             , pessoal.contrato                                                         \n";
    $stSql .= "             , (SELECT servidor_contrato_servidor.cod_contrato                                                                              \n";
    $stSql .= "                     , servidor.numcgm                                                                                                      \n";
    $stSql .= "                  FROM pessoal.servidor_contrato_servidor                                                                                   \n";
    $stSql .= "                     , pessoal.servidor                                                                                                     \n";
    $stSql .= "                 WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                      \n";
    $stSql .= "                 UNION                                                                                                                      \n";
    $stSql .= "                SELECT contrato_pensionista.cod_contrato                                                                                    \n";
    $stSql .= "                     , pensionista.numcgm                                                                                                   \n";
    $stSql .= "                  FROM pessoal.contrato_pensionista                                                                                         \n";
    $stSql .= "                     , pessoal.pensionista                                                                                                  \n";
    $stSql .= "                 WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                   \n";
    $stSql .= "                   AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor                            \n";
    $stSql .= "         WHERE evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro          \n";
    $stSql .= "           AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp             \n";
    $stSql .= "           AND evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento            \n";
    $stSql .= "           AND evento_complementar_calculado.cod_configuracao   = registro_evento_complementar.cod_configuracao      \n";
    $stSql .= "           AND registro_evento_complementar.cod_registro        = ultimo_registro_evento_complementar.cod_registro   \n";
    $stSql .= "           AND registro_evento_complementar.cod_evento          = ultimo_registro_evento_complementar.cod_evento     \n";
    $stSql .= "           AND registro_evento_complementar.cod_configuracao    = ultimo_registro_evento_complementar.cod_configuracao\n";
    $stSql .= "           AND registro_evento_complementar.timestamp           = ultimo_registro_evento_complementar.timestamp      \n";
    $stSql .= "           AND registro_evento_complementar.cod_periodo_movimentacao = contrato_servidor_complementar.cod_periodo_movimentacao \n";
    $stSql .= "           AND registro_evento_complementar.cod_complementar = contrato_servidor_complementar.cod_complementar       \n";
    $stSql .= "           AND registro_evento_complementar.cod_contrato = contrato_servidor_complementar.cod_contrato               \n";
    $stSql .= "           AND contrato_servidor_complementar.cod_contrato = servidor.cod_contrato \n";
    $stSql .= "           AND servidor.cod_contrato = contrato.cod_contrato \n";
    $stSql .= "           AND contrato.cod_contrato NOT IN (SELECT cod_contrato                       \n";
    $stSql .= "                                   FROM pessoal.contrato_servidor_caso_causa )   \n";
    $stSql .= "      GROUP BY contrato.cod_contrato                                                    \n";
    $stSql .= "             , servidor.numcgm                                                          \n";
    $stSql .= "             , contrato.registro                                                        \n";
    $stSql .= "             , registro_evento_complementar.cod_complementar                            \n";
    $stSql .= "             , registro_evento_complementar.cod_periodo_movimentacao) as contrato               \n";

    return $stSql;
}

function recuperaContratosComRegistroDeEvento(&$rsRecordSet, $stFiltro, $stOrdem, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato ";
    $stSql  = $this->montaRecuperaContratosComRegistroDeEvento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosComRegistroDeEvento()
{
    $stSql .= "SELECT *                                                                                \n";
    $stSql .= "     , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = contrato.numcgm) as nom_cgm           \n";
    $stSql .= "  FROM (SELECT registro                                                                 \n";
    $stSql .= "             , contrato.cod_contrato                                                    \n";
    $stSql .= "             , servidor.numcgm                                                          \n";
    $stSql .= "             , registro_evento_complementar.cod_complementar                            \n";
    $stSql .= "             , registro_evento_complementar.cod_periodo_movimentacao                    \n";
    $stSql .= "          FROM folhapagamento.registro_evento_complementar                              \n";
    $stSql .= "             , folhapagamento.ultimo_registro_evento_complementar                       \n";
    $stSql .= "             , folhapagamento.contrato_servidor_complementar                            \n";
    $stSql .= "             , pessoal.contrato                                                         \n";

    $stSql .= "             , (SELECT servidor_contrato_servidor.cod_contrato                                                                              \n";
    $stSql .= "                     , servidor.numcgm                                                                                                      \n";
    $stSql .= "                  FROM pessoal.servidor_contrato_servidor                                                                                   \n";
    $stSql .= "                     , pessoal.servidor                                                                                                     \n";
    $stSql .= "                 WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                      \n";
    $stSql .= "                 UNION                                                                                                                      \n";
    $stSql .= "                SELECT contrato_pensionista.cod_contrato                                                                                    \n";
    $stSql .= "                     , pensionista.numcgm                                                                                                   \n";
    $stSql .= "                  FROM pessoal.contrato_pensionista                                                                                         \n";
    $stSql .= "                     , pessoal.pensionista                                                                                                  \n";
    $stSql .= "                 WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                   \n";
    $stSql .= "                   AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor                            \n";

    $stSql .= "         WHERE registro_evento_complementar.cod_registro        = ultimo_registro_evento_complementar.cod_registro   \n";
    $stSql .= "           AND registro_evento_complementar.cod_evento          = ultimo_registro_evento_complementar.cod_evento     \n";
    $stSql .= "           AND registro_evento_complementar.cod_configuracao    = ultimo_registro_evento_complementar.cod_configuracao\n";
    $stSql .= "           AND registro_evento_complementar.timestamp           = ultimo_registro_evento_complementar.timestamp      \n";
    $stSql .= "           AND registro_evento_complementar.cod_periodo_movimentacao = contrato_servidor_complementar.cod_periodo_movimentacao \n";
    $stSql .= "           AND registro_evento_complementar.cod_complementar = contrato_servidor_complementar.cod_complementar       \n";
    $stSql .= "           AND registro_evento_complementar.cod_contrato = contrato_servidor_complementar.cod_contrato               \n";
    $stSql .= "           AND contrato_servidor_complementar.cod_contrato = servidor.cod_contrato \n";
    $stSql .= "           AND servidor.cod_contrato = contrato.cod_contrato \n";
    $stSql .= "      GROUP BY contrato.cod_contrato                                                    \n";
    $stSql .= "             , servidor.numcgm                                                          \n";
    $stSql .= "             , contrato.registro                                                        \n";
    $stSql .= "             , registro_evento_complementar.cod_periodo_movimentacao                    \n";
    $stSql .= "             , registro_evento_complementar.cod_complementar) as contrato               \n";

    return $stSql;
}

function recuperaContratosComRegistroDeEventoPorLotacao(&$rsRecordSet, $stFiltro, $stOrdem="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato ";
    $stSql  = $this->montaRecuperaContratosComRegistroDeEventoPorLotacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosComRegistroDeEventoPorLotacao()
{
    $stSql .= "SELECT contrato.*                                                                                               \n";
    $stSql .= "     , sw_cgm.numcgm                                                                                            \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                           \n";
    $stSql .= "  FROM (SELECT contrato_servidor_orgao.cod_contrato                                                             \n";
    $stSql .= "             , contrato_servidor_orgao.cod_orgao                                                                \n";
    $stSql .= "             , numcgm                                                                                           \n";
    $stSql .= "          FROM pessoal.contrato_servidor_orgao                                        \n";
    $stSql .= "             , (  SELECT cod_contrato                                                                           \n";
    $stSql .= "                       , MAX(timestamp) as timestamp                                                            \n";
    $stSql .= "                    FROM pessoal.contrato_servidor_orgao                              \n";
    $stSql .= "                GROUP BY cod_contrato) as max_contrato_servidor_orgao                                           \n";
    $stSql .= "             , pessoal.servidor_contrato_servidor                                     \n";
    $stSql .= "             , pessoal.servidor                                                       \n";
    $stSql .= "         WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                  \n";
    $stSql .= "           AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                        \n";
    $stSql .= "           AND contrato_servidor_orgao.cod_contrato = servidor_contrato_servidor.cod_contrato                   \n";
    $stSql .= "           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                  \n";
    $stSql .= "         UNION                                                                                                  \n";
    $stSql .= "        SELECT contrato_pensionista_orgao.cod_contrato                                                          \n";
    $stSql .= "             , contrato_pensionista_orgao.cod_orgao                                                             \n";
    $stSql .= "             , numcgm                                                                                           \n";
    $stSql .= "          FROM pessoal.contrato_pensionista_orgao                                     \n";
    $stSql .= "             , (  SELECT cod_contrato                                                                           \n";
    $stSql .= "                       , MAX(timestamp) as timestamp                                                            \n";
    $stSql .= "                    FROM pessoal.contrato_pensionista_orgao                           \n";
    $stSql .= "                GROUP BY cod_contrato) as max_contrato_pensionista_orgao                                        \n";
    $stSql .= "             , pessoal.contrato_pensionista                                           \n";
    $stSql .= "             , pessoal.pensionista                                                    \n";
    $stSql .= "         WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato            \n";
    $stSql .= "           AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp                  \n";
    $stSql .= "           AND contrato_pensionista_orgao.cod_contrato = contrato_pensionista.cod_contrato                      \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                     \n";
    $stSql .= "           AND contrato_pensionista.cod_pensionista = pensionista.cod_pensionista) as servidor_pensionista      \n";
    $stSql .= "     , pessoal.contrato                                                               \n";
    $stSql .= "     , folhapagamento.registro_evento_complementar                                    \n";
    $stSql .= "     , sw_cgm                                                                                                   \n";
    $stSql .= " WHERE servidor_pensionista.cod_contrato = contrato.cod_contrato                                                \n";
    $stSql .= "   AND contrato.cod_contrato = registro_evento_complementar.cod_contrato                                        \n";
    $stSql .= "   AND servidor_pensionista.numcgm = sw_cgm.numcgm                                                              \n";
    $stSql .= "   AND contrato.cod_contrato NOT IN (SELECT cod_contrato                                                        \n";
    $stSql .= "                                       FROM pessoal.contrato_servidor_caso_causa )    \n";
    $stSql .= "   AND cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."                                \n";
    $stSql .= "   AND cod_complementar = ".$this->getDado("cod_complementar")."                                                \n";
    $stSql .= "   AND cod_orgao IN (".$this->getDado("cod_orgao").")                                                           \n";
    $stSql .= "GROUP BY contrato.registro                                                                                      \n";
    $stSql .= "       , contrato.cod_contrato                                                                                  \n";
    $stSql .= "       , sw_cgm.numcgm                                                                                          \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                                         \n";

    return $stSql;
}

function recuperaContratosComRegistroDeEventoRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY $stOrdem" : " ORDER BY cod_contrato";
    $stSql = $this->montaRecuperaContratosComRegistroDeEventoRelatorio().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosComRegistroDeEventoRelatorio()
{
    $stSql .= "SELECT servidor.*                                                                                                                               \n";
    $stSql .= "  FROM (SELECT servidor_contrato_servidor.cod_contrato                                                                                          \n";
    $stSql .= "             , servidor.numcgm                                                                                                                  \n";
    $stSql .= "             , contrato_servidor_orgao.cod_orgao                                                                                                \n";
    $stSql .= "             , contrato_servidor_local.cod_local                                                                                                \n";
    $stSql .= "          FROM pessoal.servidor_contrato_servidor                                                                                               \n";
    $stSql .= "     LEFT JOIN (SELECT contrato_servidor_local.cod_contrato                                                                                     \n";
    $stSql .= "                     , contrato_servidor_local.cod_local                                                                                        \n";
    $stSql .= "                  FROM pessoal.contrato_servidor_local                                                                                          \n";
    $stSql .= "                     , (SELECT cod_contrato                                                                                                     \n";
    $stSql .= "                             , max(timestamp) as timestamp                                                                                      \n";
    $stSql .= "                          FROM pessoal.contrato_servidor_local                                                                                  \n";
    $stSql .= "                        GROUP BY cod_contrato) as max_contrato_servidor_local                                                                   \n";
    $stSql .= "                 WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                          \n";
    $stSql .= "                   AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local                    \n";
    $stSql .= "            ON servidor_contrato_servidor.cod_contrato = contrato_servidor_local.cod_local                                                      \n";
    $stSql .= "             , pessoal.servidor                                                                                                                 \n";
    $stSql .= "             , pessoal.contrato_servidor_orgao                                                                                                  \n";
    $stSql .= "             , (  SELECT cod_contrato                                                                                                           \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                                            \n";
    $stSql .= "                    FROM pessoal.contrato_servidor_orgao                                                                                        \n";
    $stSql .= "                GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                           \n";
    $stSql .= "         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                  \n";
    $stSql .= "           AND servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                                                   \n";
    $stSql .= "           AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                  \n";
    $stSql .= "           AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                                                        \n";
    $stSql .= "         UNION                                                                                                                                  \n";
    $stSql .= "        SELECT contrato_pensionista.cod_contrato                                                                                                \n";
    $stSql .= "             , pensionista.numcgm                                                                                                               \n";
    $stSql .= "             , contrato_pensionista_orgao.cod_orgao                                                                                             \n";
    $stSql .= "             , 0 as cod_local                                                                                                                   \n";
    $stSql .= "          FROM pessoal.contrato_pensionista                                                                                                     \n";
    $stSql .= "             , pessoal.pensionista                                                                                                              \n";
    $stSql .= "             , pessoal.contrato_pensionista_orgao                                                                                               \n";
    $stSql .= "             , (  SELECT cod_contrato                                                                                                           \n";
    $stSql .= "                       , max(timestamp) as timestamp                                                                                            \n";
    $stSql .= "                    FROM pessoal.contrato_pensionista_orgao                                                                                     \n";
    $stSql .= "                GROUP BY cod_contrato) as max_contrato_pensionista_orgao                                                                        \n";
    $stSql .= "         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                               \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                                                     \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato = contrato_pensionista_orgao.cod_contrato                                                      \n";
    $stSql .= "           AND contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato                                            \n";
    $stSql .= "           AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as servidor                                     \n";
    $stSql .= "     , pessoal.contrato                                                                                                                         \n";
    $stSql .= " WHERE servidor.cod_contrato IN (SELECT cod_contrato                                                                                            \n";
    $stSql .= "                                   FROM folhapagamento.registro_evento_complementar                                                             \n";
    $stSql .= "                                  WHERE cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."                               \n";
    $stSql .= "                                    AND cod_complementar = ".$this->getDado("cod_complementar").")                                              \n";
    $stSql .= "   AND servidor.cod_contrato = contrato.cod_contrato                                                                                            \n";

    return $stSql;
}

function recuperaContratosAutomaticos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosAutomaticos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosAutomaticos()
{
    $stSql .= "  SELECT registro_evento_complementar.cod_contrato                                                                               \n";
    $stSql .= "       , registro                                                                                                                \n";
    $stSql .= "       , sw_cgm.numcgm                                                                                                           \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                                                          \n";
    $stSql .= "    FROM folhapagamento.registro_evento_complementar                                                   \n";
    $stSql .= "       , folhapagamento.ultimo_registro_evento_complementar                                            \n";
    $stSql .= "       , (SELECT servidor_contrato_servidor.cod_contrato                                                                         \n";
    $stSql .= "               , servidor.numcgm                                                                                                 \n";
    $stSql .= "            FROM pessoal.servidor_contrato_servidor                                                    \n";
    $stSql .= "               , pessoal.servidor                                                                      \n";
    $stSql .= "           WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                 \n";
    $stSql .= "           UNION                                                                                                                 \n";
    $stSql .= "          SELECT contrato_pensionista.cod_contrato                                                                               \n";
    $stSql .= "               , pensionista.numcgm                                                                                              \n";
    $stSql .= "            FROM pessoal.contrato_pensionista                                                          \n";
    $stSql .= "               , pessoal.pensionista                                                                   \n";
    $stSql .= "           WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                              \n";
    $stSql .= "             AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor_pensionista           \n";
    $stSql .= "       , pessoal.contrato                                                                              \n";
    $stSql .= "       , sw_cgm                                                                                                                  \n";
    $stSql .= "   WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro                            \n";
    $stSql .= "     AND registro_evento_complementar.cod_evento = ultimo_registro_evento_complementar.cod_evento                                \n";
    $stSql .= "     AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao                    \n";
    $stSql .= "     AND registro_evento_complementar.timestamp = ultimo_registro_evento_complementar.timestamp                                  \n";
    $stSql .= "     AND registro_evento_complementar.cod_contrato  = servidor_pensionista.cod_contrato                                          \n";
    $stSql .= "     AND servidor_pensionista.cod_contrato = contrato.cod_contrato                                                               \n";
    $stSql .= "     AND servidor_pensionista.numcgm = sw_cgm.numcgm                                                                             \n";
    $stSql .= "     AND registro_evento_complementar.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."                  \n";
    $stSql .= "     AND registro_evento_complementar.cod_complementar = ".$this->getDado("cod_complementar")."                                  \n";
    $stSql .= "     AND sw_cgm.numcgm IN (".$this->getDado("numcgm").")                                                                         \n";
//    $stSql .= "     AND contrato.cod_contrato NOT IN (SELECT cod_contrato FROM pessoal.contrato_servidor_caso_causa)  \n";
    $stSql .= "GROUP BY registro_evento_complementar.cod_contrato                                                                               \n";
    $stSql .= "       , registro                                                                                                                \n";
    $stSql .= "       , sw_cgm.numcgm                                                                                                           \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                                                          \n";

    return $stSql;
}

function recuperaContratosAutomaticosFerias(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosAutomaticosFerias",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosAutomaticosFerias()
{
    $stSql .= "SELECT servidor_contrato_servidor.cod_contrato                               \n";
    $stSql .= "     , registro                                                              \n";
    $stSql .= "     , sw_cgm.numcgm                                                         \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                        \n";
    $stSql .= "  FROM pessoal.lancamento_ferias                                             \n";
    $stSql .= "     , pessoal.ferias                                                        \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                    \n";
    $stSql .= "     , pessoal.servidor                                                      \n";
    $stSql .= "     , pessoal.contrato                                                      \n";
    $stSql .= "     , (  SELECT cod_contrato                                                \n";
    $stSql .= "               , MAX(timestamp) as timestamp                                 \n";
    $stSql .= "            FROM pessoal.contrato_servidor_orgao   \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_orgao                \n";
    $stSql .= "     , pessoal.contrato_servidor_orgao             \n";
    $stSql .= "     , sw_cgm                                                                \n";
    $stSql .= " WHERE lancamento_ferias.cod_ferias = ferias.cod_ferias                      \n";
    $stSql .= "   AND ferias.cod_contrato = servidor_contrato_servidor.cod_contrato         \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor       \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm.numcgm                                       \n";
    $stSql .= "   AND ferias.cod_contrato = contrato.cod_contrato                           \n";
    $stSql .= "   AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                  \n";
    $stSql .= "   AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                        \n";
    $stSql .= "   AND contrato_servidor_orgao.cod_contrato = contrato.cod_contrato          \n";
    $stSql .= "   AND cod_tipo = 3                                                          \n";
    $stSql .= "   AND mes_competencia = '".$this->getDado("mes")."'                         \n";
    $stSql .= "   AND ano_competencia = '".$this->getDado("ano")."'                         \n";

    return $stSql;
}

function recuperaContratosComRegistroDeEventoReduzido(&$rsRecordSet, $stFiltro="", $stOrdem="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato ";
    $stSql  = $this->montaRecuperaContratosComRegistroDeEventoReduzido().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosComRegistroDeEventoReduzido()
{
    $stSql .= "SELECT servidor_pensionista.*                                                                                      \n";
    $stSql .= "  FROM (SELECT servidor_contrato_servidor.cod_contrato                                                             \n";
    $stSql .= "             , servidor.numcgm                                                                                     \n";
    $stSql .= "          FROM pessoal.servidor_contrato_servidor                                        \n";
    $stSql .= "             , pessoal.servidor                                                          \n";
    $stSql .= "         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                     \n";
    $stSql .= "         UNION                                                                                                     \n";
    $stSql .= "        SELECT contrato_pensionista.cod_contrato                                                                   \n";
    $stSql .= "             , pensionista.numcgm                                                                                  \n";
    $stSql .= "          FROM pessoal.contrato_pensionista                                              \n";
    $stSql .= "             , pessoal.pensionista                                                       \n";
    $stSql .= "         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                  \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor_pensionista\n";
    $stSql .= " WHERE NOT EXISTS (SELECT 1                                                                                        \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_caso_causa                           \n";
    $stSql .= "                    WHERE servidor_pensionista.cod_contrato = contrato_servidor_caso_causa.cod_contrato)           \n";
    $stSql .= "   AND EXISTS (SELECT 1                                                                                            \n";
    $stSql .= "                 FROM folhapagamento.registro_evento_complementar                        \n";
    $stSql .= "                WHERE servidor_pensionista.cod_contrato = registro_evento_complementar.cod_contrato                \n";
    $stSql .= "                  AND registro_evento_complementar.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")." \n";
    $stSql .= "                  AND registro_evento_complementar.cod_complementar = ".$this->getDado("cod_complementar").")\n";

    return $stSql;
}

function montaRecuperarRegistroContratoComplementar()
{
    $stSql .= "SELECT to_real(registro_evento_complementar.valor) as valor                                                                                                  \n";
    $stSql .= "     , to_real(registro_evento_complementar.quantidade) as quantidade                                                                                        \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_complementar.cod_contrato) as matricula         \n";
    $stSql .= "     , registro_evento_complementar.cod_contrato                                                                                                             \n";
    $stSql .= "     , servidor.numcgm                                                                                                                                       \n";
    $stSql .= "     , configuracao_evento.descricao as descricao                                                                                                            \n";
    $stSql .= "     , registro_evento_complementar.cod_periodo_movimentacao                                                                                                 \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                                \n";
    $stSql .= " FROM folhapagamento.registro_evento_complementar                                                                                  \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_complementar                                                                          \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                          \n";
    $stSql .= "     , pessoal.servidor                                                                                                            \n";
    $stSql .= "     , folhapagamento.evento                                                                                                       \n";
    $stSql .= "     , folhapagamento.configuracao_evento                                                                                                                    \n";
    $stSql .= "WHERE registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro                                                           \n";
    $stSql .= "  AND registro_evento_complementar.timestamp = ultimo_registro_evento_complementar.timestamp                                                                 \n";
    $stSql .= "  AND registro_evento_complementar.cod_evento = ultimo_registro_evento_complementar.cod_evento                                                               \n";
    $stSql .= "  AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                    \n";
    $stSql .= "  AND registro_evento_complementar.cod_configuracao = configuracao_evento.cod_configuracao                                                                   \n";
    $stSql .= "  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                        \n";
    $stSql .= "  AND registro_evento_complementar.cod_evento = evento.cod_evento                                                                                            \n";

    return $stSql;
}

function montaRecuperarRegistroContratoComplementarComPensionista($stFiltro,$stOrdem)
{
    $stSql .= "SELECT to_real(evento_complementar_calculado.valor) as valor                                                                                                 \n";
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
    $stSql .= $stFiltro;
    $stSql .= " UNION                                                                                                                                                        \n";
    $stSql .= " SELECT to_real(evento_complementar_calculado.valor) as valor                                                                                                \n";
    $stSql .= "      , to_real(evento_complementar_calculado.quantidade) as quantidade                                                                                      \n";
    $stSql .= "      , (select registro from pessoal.contrato where cod_contrato = registro_evento_complementar.cod_contrato) as matricula                                  \n";
    $stSql .= "      , registro_evento_complementar.cod_contrato                                                                                                            \n";
    $stSql .= "      , pensionista.numcgm                                                                                                                                   \n";
    $stSql .= "      , configuracao_evento.descricao as descricao                                                                                                           \n";
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

function recuperarRegistroContratoComplementarComPensionista(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarRegistroContratoComplementarComPensionista($stFiltro,$stOrdem);
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaRegistroContratoComplementar(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperarRegistroContratoComplementar().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function deletarRegistroEvento($boTransacao="")
{
    return $this->executaRecupera("montaDeletarRegistro", $rsRecordSet, "", "", $boTransacao);
}

function montaDeletarRegistro()
{
    $stSql  = "SELECT criarBufferTexto('stEntidade','".Sessao::getEntidade()."');       \n";
    $stSql .= "SELECT criarBufferTexto('stTipoFolha','C');                              \n";
    $stSql .= "SELECT deletarRegistroEvento(".$this->getDado("cod_registro")."    \n";
    $stSql .= "                            ,".$this->getDado("cod_evento")."      \n";
    $stSql .= "                           ,'".$this->getDado("desdobramento")."'  \n";
    $stSql .= "                           ,'".$this->getDado("timestamp")."');    \n";

    return $stSql;
}

}
