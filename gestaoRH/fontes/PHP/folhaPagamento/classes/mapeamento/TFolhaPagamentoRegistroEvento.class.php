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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.REGISTRO_EVENTO
    * Data de Criação: 04/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-11-20 13:04:07 -0200 (Ter, 20 Nov 2007) $

    * Casos de uso: uc-04.05.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.REGISTRO_EVENTO
  * Data de Criação: 04/11/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoRegistroEvento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoRegistroEvento()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.registro_evento');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_registro,timestamp,cod_evento');

    $this->AddCampo( 'cod_registro', 'integer'  , true , ''    , true , 'TFolhaPagamentoRegistroEventoPeriodo' );
    $this->AddCampo( 'timestamp'   , 'timestamp_now', true, '' , true , false                                  );
    $this->AddCampo( 'cod_evento'  , 'integer'  , true , ''    , true , 'TFolhaPagamentoEvento'                );
    $this->AddCampo( 'valor'       , 'numeric'  , false, '15,2', false, false                                  );
    $this->AddCampo( 'quantidade'  , 'numeric'  , false, '15,2', false, false                                  );
    $this->AddCampo( 'proporcional', 'boolean'  , true , ''    , false, false                                  );
    $this->AddCampo( 'automatico'  , 'boolean'  , true , ''    , false, false                                  );
}

function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY re.timestamp ";
    $stSql  = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSql = "";
    $stSql .= "    SELECT registro_evento.*\n";
    $stSql .= "         , contrato.*\n";
    $stSql .= "         , evento.codigo\n";
    $stSql .= "         , CASE evento_calculado.desdobramento\n";
    $stSql .= "            WHEN 'A' THEN trim(evento.descricao) || ' Abono Férias'\n";
    $stSql .= "            WHEN 'F' THEN trim(evento.descricao) || ' Férias no Mês'\n";
    $stSql .= "            WHEN 'D' THEN trim(evento.descricao) || ' Adiant. Férias'\n";
    $stSql .= "           ELSE trim(evento.descricao) END as descricao\n";
    $stSql .= "         , evento.tipo\n";
    $stSql .= "         , evento.fixado\n";
    $stSql .= "         , evento.natureza\n";
    $stSql .= "         , evento.limite_calculo\n";
    $stSql .= "         , evento.evento_sistema\n";
    $stSql .= "         , CASE evento.natureza\n";
    $stSql .= "            WHEN 'P' THEN 'Proventos'\n";
    $stSql .= "            WHEN 'D' THEN 'Descontos'\n";
    $stSql .= "            WHEN 'B' THEN 'Base'\n";
    $stSql .= "           END as proventos_descontos\n";
    $stSql .= "         , parcela\n";
    $stSql .= "         , evento_evento.observacao\n";
    $stSql .= "      FROM folhapagamento.evento\n";
    $stSql .= "INNER JOIN folhapagamento.evento_evento\n";
    $stSql .= "        ON evento.cod_evento = evento_evento.cod_evento\n";
    $stSql .= "INNER JOIN folhapagamento.registro_evento\n";
    $stSql .= "        ON evento.cod_evento = registro_evento.cod_evento\n";
    $stSql .= "INNER JOIN folhapagamento.registro_evento_periodo\n";
    $stSql .= "        ON registro_evento.cod_registro = registro_evento_periodo.cod_registro\n";
    $stSql .= "INNER JOIN folhapagamento.ultimo_registro_evento\n";
    $stSql .= "        ON registro_evento.cod_evento = ultimo_registro_evento.cod_evento\n";
    $stSql .= "       AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro\n";
    $stSql .= "       AND registro_evento.timestamp = ultimo_registro_evento.timestamp\n";
    $stSql .= "INNER JOIN folhapagamento.periodo_movimentacao\n";
    $stSql .= "        ON registro_evento_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao\n";
    $stSql .= "INNER JOIN pessoal.contrato\n";
    $stSql .= "        ON registro_evento_periodo.cod_contrato = contrato.cod_contrato\n";
    $stSql .= "INNER JOIN (\n";
    $stSql .= "             SELECT servidor_contrato_servidor.cod_contrato\n";
    $stSql .= "                  , servidor.numcgm\n";
    $stSql .= "                  , contrato_servidor_orgao.cod_orgao\n";
    $stSql .= "               FROM pessoal.servidor_contrato_servidor\n";
    $stSql .= "                  , pessoal.servidor\n";
    $stSql .= "                  , pessoal.contrato_servidor_orgao\n";
    $stSql .= "              WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor\n";
    $stSql .= "                AND servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato\n";
    $stSql .= "                AND contrato_servidor_orgao.timestamp = ( SELECT timestamp\n";
    $stSql .= "                                                            FROM pessoal.contrato_servidor_orgao as contrato_servidor_orgao_interno\n";
    $stSql .= "                                                           WHERE contrato_servidor_orgao_interno.cod_contrato = contrato_servidor_orgao.cod_contrato\n";
    $stSql .= "                                                        ORDER BY timestamp DESC\n";
    $stSql .= "                                                           LIMIT 1 )\n";
    $stSql .= "              UNION\n";
    $stSql .= "             SELECT contrato_pensionista.cod_contrato\n";
    $stSql .= "                  , pensionista.numcgm\n";
    $stSql .= "                  , contrato_pensionista_orgao.cod_orgao\n";
    $stSql .= "               FROM pessoal.contrato_pensionista\n";
    $stSql .= "                  , pessoal.pensionista\n";
    $stSql .= "                  , pessoal.contrato_pensionista_orgao\n";
    $stSql .= "              WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista\n";
    $stSql .= "                AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente\n";
    $stSql .= "                AND contrato_pensionista.cod_contrato = contrato_pensionista_orgao.cod_contrato\n";
    $stSql .= "                AND contrato_pensionista_orgao.timestamp = (  SELECT timestamp\n";
    $stSql .= "                                                                FROM pessoal.contrato_pensionista_orgao as contrato_pensionista_orgao_interno\n";
    $stSql .= "                                                               WHERE contrato_pensionista_orgao_interno.cod_contrato = contrato_pensionista_orgao.cod_contrato\n";
    $stSql .= "                                                            ORDER BY timestamp DESC\n";
    $stSql .= "                                                               LIMIT 1 )\n";
    $stSql .= "           ) as servidor\n";
    $stSql .= "        ON contrato.cod_contrato = servidor.cod_contrato\n";
    $stSql .= " LEFT JOIN folhapagamento.evento_calculado\n";
    $stSql .= "        ON ultimo_registro_evento.cod_registro = evento_calculado.cod_registro\n";
    $stSql .= "       AND ultimo_registro_evento.cod_evento   = evento_calculado.cod_evento\n";
    $stSql .= "       AND ultimo_registro_evento.timestamp    = evento_calculado.timestamp_registro\n";
    $stSql .= " LEFT JOIN folhapagamento.registro_evento_parcela\n";
    $stSql .= "        ON registro_evento.cod_registro     = registro_evento_parcela.cod_registro\n";
    $stSql .= "       AND registro_evento.timestamp        = registro_evento_parcela.timestamp\n";
    $stSql .= " LEFT JOIN (\n";
    $stSql .= "             SELECT local.*\n";
    $stSql .= "               FROM pessoal.contrato_servidor_local as local\n";
    $stSql .= "              WHERE local.timestamp = (  SELECT timestamp\n";
    $stSql .= "                                           FROM pessoal.contrato_servidor_local as local_interno\n";
    $stSql .= "                                          WHERE local_interno.cod_contrato = local.cod_contrato\n";
    $stSql .= "                                       ORDER BY timestamp DESC\n";
    $stSql .= "                                          LIMIT 1\n";
    $stSql .= "                                       )\n";
    $stSql .= "           ) as local\n";
    $stSql .= "        ON contrato.cod_contrato = local.cod_contrato\n";
    $stSql .= "     WHERE evento_evento.timestamp = ( SELECT timestamp\n";
    $stSql .= "                                         FROM folhapagamento.evento_evento evento_evento_interna\n";
    $stSql .= "                                        WHERE evento_evento_interna.cod_evento = evento_evento.cod_evento\n";
    $stSql .= "                                     ORDER BY timestamp DESC\n";
    $stSql .= "                                        LIMIT 1\n";
    $stSql .= "                               )\n";

    return $stSql;
}

function recuperaRelacionamentoConfiguracao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY descricao ";
    $stSql  = $this->montaRecuperaRelacionamentoConfiguracao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoConfiguracao()
{
    $stSql .= "    SELECT evento.*\n";
    $stSql .= "         , evento_evento.observacao\n";
    $stSql .= "         , evento_evento.valor_quantidade\n";
    $stSql .= "         , configuracao_evento_caso.cod_caso\n";
    $stSql .= "         , configuracao_evento_caso.cod_configuracao\n";
    $stSql .= "         , configuracao_evento_caso.timestamp\n";
    $stSql .= "      FROM folhapagamento.evento\n";
    $stSql .= "INNER JOIN folhapagamento.evento_evento\n";
    $stSql .= "        ON evento.cod_evento = evento_evento.cod_evento\n";
    $stSql .= "INNER JOIN folhapagamento.configuracao_evento_caso\n";
    $stSql .= "        ON evento_evento.cod_evento = configuracao_evento_caso.cod_evento\n";
    $stSql .= "       AND evento_evento.timestamp = configuracao_evento_caso.timestamp\n";
    $stSql .= "INNER JOIN folhapagamento.configuracao_evento_caso_sub_divisao as sub_divisao\n";
    $stSql .= "        ON configuracao_evento_caso.cod_caso          = sub_divisao.cod_caso\n";
    $stSql .= "       AND configuracao_evento_caso.cod_evento        = sub_divisao.cod_evento\n";
    $stSql .= "       AND configuracao_evento_caso.cod_configuracao  = sub_divisao.cod_configuracao\n";
    $stSql .= "       AND configuracao_evento_caso.timestamp         = sub_divisao.timestamp\n";
    $stSql .= "INNER JOIN folhapagamento.configuracao_evento_caso_cargo as cargo\n";
    $stSql .= "        ON configuracao_evento_caso.cod_caso          = cargo.cod_caso\n";
    $stSql .= "       AND configuracao_evento_caso.cod_evento        = cargo.cod_evento\n";
    $stSql .= "       AND configuracao_evento_caso.cod_configuracao  = cargo.cod_configuracao\n";
    $stSql .= "       AND configuracao_evento_caso.timestamp         = cargo.timestamp\n";
    $stSql .= " LEFT JOIN folhapagamento.configuracao_evento_caso_especialidade as especialidade\n";
    $stSql .= "        ON cargo.cod_caso                             = especialidade.cod_caso\n";
    $stSql .= "       AND cargo.cod_evento                           = especialidade.cod_evento\n";
    $stSql .= "       AND cargo.cod_configuracao                     = especialidade.cod_configuracao\n";
    $stSql .= "       AND cargo.timestamp                            = especialidade.timestamp\n";
    $stSql .= "       AND cargo.cod_cargo                            = especialidade.cod_cargo\n";
    $stSql .= "    WHERE evento_evento.timestamp = ( SELECT timestamp\n";
    $stSql .= "                                        FROM folhapagamento.evento_evento as evento_evento_interno\n";
    $stSql .= "                                       WHERE evento_evento_interno.cod_evento = evento_evento.cod_evento\n";
    $stSql .= "                                    ORDER BY timestamp desc\n";
    $stSql .= "                                       LIMIT 1 )\n";

    return $stSql;
}

function recuperaContratosComRegistroDeEvento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY numcgm,contrato.registro";
    $stSql  = $this->montaRecuperaContratosComRegistroDeEvento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosComRegistroDeEvento()
{
    $stSql .= "  SELECT registro                                                                \n";
    $stSql .= "       , contrato.cod_contrato                                                   \n";
    $stSql .= "       , servidor.numcgm                                                         \n";
    $stSql .= "       , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm  \n";
    $stSql .= "    FROM folhapagamento.registro_evento_periodo                                  \n";
    $stSql .= "       , folhapagamento.registro_evento                                          \n";
    $stSql .= "       , folhapagamento.ultimo_registro_evento                                   \n";
    $stSql .= "       , pessoal.contrato                                                        \n";
    $stSql .= "           , (SELECT servidor_contrato_servidor.cod_contrato                             \n";
    $stSql .= "                   , servidor.numcgm                                                     \n";
    $stSql .= "                FROM pessoal.servidor_contrato_servidor                                  \n";
    $stSql .= "                   , pessoal.servidor                                                    \n";
    $stSql .= "               WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor     \n";
    $stSql .= "               UNION                                                                     \n";
    $stSql .= "              SELECT contrato_pensionista.cod_contrato                                   \n";
    $stSql .= "                   , pensionista.numcgm                                                  \n";
    $stSql .= "                FROM pessoal.contrato_pensionista                                        \n";
    $stSql .= "                   , pessoal.pensionista                                                 \n";
    $stSql .= "               WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista  \n";
    $stSql .= "                 AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor\n";
    $stSql .= "   WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro     \n";
    $stSql .= "     AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro      \n";
    $stSql .= "     AND registro_evento.cod_evento   = ultimo_registro_evento.cod_evento        \n";
    $stSql .= "     AND registro_evento.timestamp    = ultimo_registro_evento.timestamp         \n";
    $stSql .= "     AND registro_evento_periodo.cod_contrato = contrato.cod_contrato            \n";
    $stSql .= "     AND contrato.cod_contrato = servidor.cod_contrato         \n";
    $stSql .= "     AND registro_evento_periodo.cod_periodo_movimentacao = ".$this->getDado('cod_periodo_movimentacao')." \n";
    $stSql .= "     AND contrato.cod_contrato NOT IN (SELECT cod_contrato                       \n";
    $stSql .= "                                   FROM pessoal.contrato_servidor_caso_causa )   \n";
    $stSql .= "GROUP BY contrato.cod_contrato                                    \n";
    $stSql .= "       , contrato.registro                                                       \n";
    $stSql .= "       , servidor.numcgm                                                         \n";

    return $stSql;
}

function recuperaContratosComRegistroDeEventoPorCgm(&$rsRecordSet, $stFiltro, $stOrdem, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_contrato ";
    $stSql  = $this->montaRecuperaContratosComRegistroDeEventoPorCgm().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosComRegistroDeEventoPorCgm()
{
    $stSql .= "SELECT *                                                                                \n";
    $stSql .= "  FROM (SELECT registro                                                                 \n";
    $stSql .= "             , registro_evento_periodo.cod_contrato                                     \n";
    $stSql .= "             , servidor.numcgm                                                          \n";
    $stSql .= "          FROM folhapagamento.evento_calculado                                          \n";
    $stSql .= "             , folhapagamento.registro_evento                                           \n";
    $stSql .= "             , (  SELECT cod_registro                                                   \n";
    $stSql .= "                       , max(timestamp) as timestamp                                    \n";
    $stSql .= "                    FROM folhapagamento.registro_evento                                 \n";
    $stSql .= "                GROUP BY cod_registro) as max_registro_evento                           \n";
    $stSql .= "             , folhapagamento.registro_evento_periodo                                   \n";
    $stSql .= "             , pessoal.contrato                                                         \n";
//    $stSql .= "             , pessoal.servidor_contrato_servidor                                       \n";
//    $stSql .= "             , pessoal.servidor                                                         \n";
    $stSql .= "           , (SELECT servidor_contrato_servidor.cod_contrato                             \n";
    $stSql .= "                   , servidor.numcgm                                                     \n";
    $stSql .= "                FROM pessoal.servidor_contrato_servidor                                  \n";
    $stSql .= "                   , pessoal.servidor                                                    \n";
    $stSql .= "               WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor     \n";
    $stSql .= "               UNION                                                                     \n";
    $stSql .= "              SELECT contrato_pensionista.cod_contrato                                   \n";
    $stSql .= "                   , pensionista.numcgm                                                  \n";
    $stSql .= "                FROM pessoal.contrato_pensionista                                        \n";
    $stSql .= "                   , pessoal.pensionista                                                 \n";
    $stSql .= "               WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista  \n";
    $stSql .= "                 AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor\n";

    $stSql .= "         WHERE evento_calculado.cod_registro = registro_evento.cod_registro             \n";
    $stSql .= "           AND evento_calculado.timestamp_registro    = registro_evento.timestamp       \n";
    $stSql .= "           AND evento_calculado.cod_evento   = registro_evento.cod_evento               \n";
    $stSql .= "           AND registro_evento_periodo.cod_registro = registro_evento.cod_registro      \n";
    $stSql .= "           AND registro_evento.cod_registro         = max_registro_evento.cod_registro  \n";
    $stSql .= "           AND registro_evento.timestamp            = max_registro_evento.timestamp     \n";
    $stSql .= "           AND registro_evento_periodo.cod_contrato = contrato.cod_contrato             \n";
    $stSql .= "           AND contrato.cod_contrato = servidor.cod_contrato          \n";
    $stSql .= "      GROUP BY registro_evento_periodo.cod_contrato                                     \n";
    $stSql .= "             , servidor.numcgm                                                          \n";
    $stSql .= "             , contrato.registro) as contrato                                           \n";

    return $stSql;
}

function recuperaEventosPorContratoEPeriodo(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if( $this->getDado('cod_periodo_movimentacao') )
        $stFiltro  = " AND registro_evento_periodo.cod_periodo_movimentacao = ".$this->getDado('cod_periodo_movimentacao')."\n";
    if( $this->getDado('registro') )
        $stFiltro .= " AND contrato.registro = ".$this->getDado('registro')."\n";
    if( $this->getDado('codigo') )
        $stFiltro .= " AND evento.codigo = '".$this->getDado('codigo')."'\n";

    $stFiltro = ( $stFiltro != "" ) ? " WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";

    $stSql  = $this->montaRecuperaEventosPorContratoEPeriodo().$stFiltro;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventosPorContratoEPeriodo()
{
    $stSql .= " SELECT registro_evento_periodo.cod_periodo_movimentacao                                                                     \n";
    $stSql .= "      , contrato.registro                                                                                                           \n";
    $stSql .= "      , evento.codigo                                                                                                        \n";
    $stSql .= "   FROM folhapagamento.registro_evento                                                                                                      \n";
    $stSql .= "   JOIN folhapagamento.ultimo_registro_evento                                                                                                               \n";
    $stSql .= "     ON registro_evento.cod_registro = ultimo_registro_evento.cod_registro                                                        \n";
    $stSql .= "   JOIN folhapagamento.evento                                                                                                               \n";
    $stSql .= "     ON evento.cod_evento = registro_evento.cod_evento                                                        \n";
    $stSql .= "   JOIN folhapagamento.registro_evento_periodo                                                                                              \n";
    $stSql .= "     ON registro_evento_periodo.cod_registro = registro_evento.cod_registro                                   \n";
    $stSql .= "   JOIN folhapagamento.contrato_servidor_periodo                                                                                            \n";
    $stSql .= "     ON contrato_servidor_periodo.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao \n";
    $stSql .= "    AND contrato_servidor_periodo.cod_contrato             = registro_evento_periodo.cod_contrato             \n";
    $stSql .= "   JOIN pessoal.contrato_servidor                                                                                                           \n";
    $stSql .= "     ON contrato_servidor.cod_contrato = contrato_servidor_periodo.cod_contrato                                      \n";
    $stSql .= "   JOIN pessoal.contrato                                                                                                                    \n";
    $stSql .= "     ON contrato.cod_contrato = contrato_servidor.cod_contrato                                                              \n";

    return $stSql;
}

function recuperaRegistrosEventosRegistradosPorUsuario(&$rsRecordSet,$stFiltro,$stOrdem,$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_evento ";
    $stSql  = $this->montaRecuperaRegistrosEventosRegistradosPorUsuario().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRegistrosEventosRegistradosPorUsuario()
{
    $stSql .= "SELECT ultimo_registro_evento.*                                                                     \n";
    $stSql .= "  FROM folhapagamento.registro_evento_periodo                                                       \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento                                                        \n";
    $stSql .= "     , folhapagamento.registro_evento                                                               \n";
    $stSql .= "     , folhapagamento.evento                                                                        \n";
    $stSql .= " WHERE registro_evento_periodo.cod_registro             = registro_evento.cod_registro              \n";
    $stSql .= "   AND registro_evento.cod_registro                     = ultimo_registro_evento.cod_registro       \n";
    $stSql .= "   AND registro_evento.cod_evento                       = ultimo_registro_evento.cod_evento         \n";
    $stSql .= "   AND registro_evento.timestamp                        = ultimo_registro_evento.timestamp          \n";
    $stSql .= "   AND registro_evento.cod_evento                       = evento.cod_evento                         \n";
    $stSql .= "   AND evento.evento_sistema                            = false                                     \n";
    $stSql .= "   AND evento.natureza                                 != 'B'                                       \n";

    return $stSql;
}

function recuperaRegistrosDeEventos(&$rsRecordSet,$stFiltro,$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY descricao ";
    $stSql  = $this->montaRecuperaRegistrosDeEventos().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRegistrosDeEventos()
{
    $stSql = "
                SELECT registro_evento_periodo.*
                     , registro_evento.*
                     , evento.codigo
                     , CASE evento_calculado.desdobramento
                       WHEN 'A' THEN trim(evento.descricao) || ' Abono Férias'
                       WHEN 'F' THEN trim(evento.descricao) || ' Férias no Mês'
                       WHEN 'D' THEN trim(evento.descricao) || ' Adiant. Férias'
                       ELSE trim(evento.descricao) END as descricao
                     , evento_calculado.desdobramento
                     , evento.tipo
                     , evento.fixado
                     , evento.natureza
                     , evento.limite_calculo
                     , evento.evento_sistema
                     , CASE evento.natureza
                       WHEN 'P' THEN 'Proventos'
                       WHEN 'D' THEN 'Descontos'
                       WHEN 'B' THEN 'Base'
                       END as proventos_descontos
                     , registro_evento_parcela.parcela
                     , registro_evento_parcela.mes_carencia
                     , registro
                     , evento_evento.observacao
                     
                  FROM folhapagamento.ultimo_registro_evento
                  
            INNER JOIN folhapagamento.registro_evento
                    ON ultimo_registro_evento.cod_evento = registro_evento.cod_evento
                   AND ultimo_registro_evento.cod_registro = registro_evento.cod_registro
                   AND ultimo_registro_evento.timestamp = registro_evento.timestamp
                   
            INNER JOIN folhapagamento.evento
                    ON evento.cod_evento = registro_evento.cod_evento
                    
            INNER JOIN folhapagamento.evento_evento
                    ON evento_evento.cod_evento = registro_evento.cod_evento
                    
            INNER JOIN folhapagamento.registro_evento_periodo
                    ON registro_evento.cod_registro = registro_evento_periodo.cod_registro
                    
            INNER JOIN pessoal.contrato
                    ON registro_evento_periodo.cod_contrato = contrato.cod_contrato
                    
             LEFT JOIN folhapagamento.evento_calculado
                    ON ultimo_registro_evento.cod_evento = evento_calculado.cod_evento
                   AND ultimo_registro_evento.cod_registro = evento_calculado.cod_registro
                   AND ultimo_registro_evento.timestamp = evento_calculado.timestamp\n";
                   
    if ($this->getDado("desdobramento")) {
        $stSql .= "       AND (trim(evento_calculado.desdobramento) = '' OR evento_calculado.desdobramento = NULL) \n";
    }
    
    $stSql .="  LEFT JOIN folhapagamento.registro_evento_parcela
                       ON ultimo_registro_evento.cod_evento = registro_evento_parcela.cod_evento
                      AND ultimo_registro_evento.cod_registro = registro_evento_parcela.cod_registro
                      AND ultimo_registro_evento.timestamp = registro_evento_parcela.timestamp
                      
                    WHERE evento_evento.timestamp = ( SELECT timestamp
                                                       FROM folhapagamento.evento_evento as evento_evento_interno
                                                      WHERE evento_evento_interno.cod_evento = evento.cod_evento
                                                   ORDER BY timestamp DESC
                                                      LIMIT 1 )\n";

    return $stSql;
}

function recuperaContratosAutomaticos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosAutomaticos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosAutomaticos()
{
    $stSql .= "  SELECT registro_evento_periodo.cod_contrato \n";
    $stSql .= "       , registro \n";
    $stSql .= "       , sw_cgm.numcgm \n";
    $stSql .= "       , sw_cgm.nom_cgm \n";
    $stSql .= "    FROM folhapagamento.registro_evento_periodo \n";
    $stSql .= "       , folhapagamento.registro_evento \n";
    $stSql .= "       , folhapagamento.ultimo_registro_evento \n";
    $stSql .= "           , (SELECT servidor_contrato_servidor.cod_contrato                             \n";
    $stSql .= "                   , servidor.numcgm                                                     \n";
    $stSql .= "                FROM pessoal.servidor_contrato_servidor                                  \n";
    $stSql .= "                   , pessoal.servidor                                                    \n";
    $stSql .= "               WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor     \n";
    $stSql .= "               UNION                                                                     \n";
    $stSql .= "              SELECT contrato_pensionista.cod_contrato                                   \n";
    $stSql .= "                   , pensionista.numcgm                                                  \n";
    $stSql .= "                FROM pessoal.contrato_pensionista                                        \n";
    $stSql .= "                   , pessoal.pensionista                                                 \n";
    $stSql .= "               WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista  \n";
    $stSql .= "                 AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor_contrato_servidor\n";
    $stSql .= "       , pessoal.contrato \n";
    $stSql .= "       , sw_cgm \n";
    $stSql .= "   WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro \n";
    $stSql .= "     AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro \n";
    $stSql .= "     AND registro_evento_periodo.cod_contrato  = servidor_contrato_servidor.cod_contrato \n";
    $stSql .= "     AND servidor_contrato_servidor.cod_contrato = contrato.cod_contrato \n";
    $stSql .= "     AND servidor_contrato_servidor.numcgm = sw_cgm.numcgm \n";
    $stSql .= "     AND cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")." \n";
    $stSql .= "     AND sw_cgm.numcgm IN (".$this->getDado("numcgm").") \n";
    $stSql .= "     AND contrato.cod_contrato NOT IN (SELECT cod_contrato FROM pessoal.contrato_servidor_caso_causa) \n";
    $stSql .= "GROUP BY registro_evento_periodo.cod_contrato \n";
    $stSql .= "       , registro \n";
    $stSql .= "       , sw_cgm.numcgm \n";
    $stSql .= "       , sw_cgm.nom_cgm \n";
    $stSql .= " \n";

    return $stSql;
}

function recuperaTodosRegistrosEventos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaTodosRegistrosEventos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaTodosRegistrosEventos()
{
    $stSql .= "     SELECT *                                                                \n";
    $stSql .= "       FROM folhapagamento.registro_evento_periodo  \n";
    $stSql .= " INNER JOIN folhapagamento.registro_evento          \n";
    $stSql .= "    ON registro_evento_periodo.cod_registro = registro_evento.cod_registro   \n";

    return $stSql;
}

function recuperaRegistrosEventos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaRegistrosEventos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaRegistrosEventos()
{
    $stSql .= "SELECT registro_evento.*                                                             \n";
    $stSql .= "  FROM folhapagamento.registro_evento_periodo               \n";
    $stSql .= "  JOIN folhapagamento.registro_evento                       \n";
    $stSql .= "    ON registro_evento_periodo.cod_registro = registro_evento.cod_registro           \n";
    $stSql .= "  JOIN folhapagamento.ultimo_registro_evento                \n";
    $stSql .= "    ON registro_evento.cod_registro = ultimo_registro_evento.cod_registro            \n";

    return $stSql;
}

}
