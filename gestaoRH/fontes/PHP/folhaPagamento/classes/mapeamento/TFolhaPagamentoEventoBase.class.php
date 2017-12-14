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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.EVENTO_BASE
    * Data de Criação: 19/12/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.EVENTO_BASE
  * Data de Criação: 19/12/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoEventoBase extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoEventoBase()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.evento_base");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_evento,cod_evento_base,cod_caso,cod_configuracao,timestamp,cod_caso_base,cod_configuracao_base,timestamp_base');

    $this->AddCampo('cod_evento','integer',true,'',true,true);
    $this->AddCampo('cod_evento_base','integer',true,'',true,true);
    $this->AddCampo('cod_caso','integer',true,'',true,true);
    $this->AddCampo('cod_configuracao','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',true,'',true,true);
    $this->AddCampo('cod_caso_base','integer',true,'',true,true);
    $this->AddCampo('cod_configuracao_base','integer',true,'',true,true);
    $this->AddCampo('timestamp_base','timestamp',true,'',true,true);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT evento_base.*                                                                                            \n";
    $stSql .= "     , evento.codigo                                                                                        \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                                                    \n";
    $stSql .= "  FROM ( SELECT evento_base.cod_evento                                                                          \n";
    $stSql .= "              , evento_base.cod_evento_base                                                                     \n";
    $stSql .= "              , evento_base.cod_configuracao                                                                    \n";
    $stSql .= "              , max(evento_base.timestamp) as timestamp                                                         \n";
    $stSql .= "              , max(evento_base.cod_caso) as cod_caso                                                           \n";
    $stSql .= "              , evento_base.cod_caso_base                                                                       \n";
    $stSql .= "              , evento_base.cod_configuracao_base                                                               \n";
    $stSql .= "              , evento_base.timestamp_base                                                                      \n";
    $stSql .= "           FROM folhapagamento.evento_base                                                                      \n";
    $stSql .= "              , folhapagamento.configuracao_evento_caso                                                         \n";
    $stSql .= "              , folhapagamento.evento_configuracao_evento                                                       \n";
    $stSql .= "              , folhapagamento.evento_evento                                                                    \n";
    $stSql .= "              , (  SELECT cod_evento                                                                            \n";
    $stSql .= "                        , max(timestamp) as timestamp                                                           \n";
    $stSql .= "                     FROM folhapagamento.evento_evento                                                          \n";
    $stSql .= "                 GROUP BY cod_evento ) as max_evento_evento                                                     \n";
    $stSql .= "          WHERE evento_base.cod_evento_base       = configuracao_evento_caso.cod_evento                         \n";
    $stSql .= "            AND evento_base.cod_configuracao_base = configuracao_evento_caso.cod_configuracao                   \n";
    $stSql .= "            AND evento_base.cod_caso_base         = configuracao_evento_caso.cod_caso                           \n";
    $stSql .= "            AND evento_base.timestamp_base        = configuracao_evento_caso.timestamp                          \n";
    $stSql .= "            AND configuracao_evento_caso.cod_evento       = evento_configuracao_evento.cod_evento               \n";
    $stSql .= "            AND configuracao_evento_caso.cod_configuracao = evento_configuracao_evento.cod_configuracao         \n";
    $stSql .= "            AND configuracao_evento_caso.timestamp        = evento_configuracao_evento.timestamp                \n";
    $stSql .= "            AND evento_configuracao_evento.cod_evento = evento_evento.cod_evento                                \n";
    $stSql .= "            AND evento_configuracao_evento.timestamp  = evento_evento.timestamp                                 \n";
    $stSql .= "            AND evento_evento.cod_evento = max_evento_evento.cod_evento                                         \n";
    $stSql .= "            AND evento_evento.timestamp  = max_evento_evento.timestamp                                          \n";
    $stSql .= "       GROUP BY evento_base.cod_evento                                                                          \n";
    $stSql .= "              , evento_base.cod_evento_base                                                                     \n";
    $stSql .= "              , evento_base.cod_configuracao                                                                    \n";
    $stSql .= "              , evento_base.cod_caso_base                                                                       \n";
    $stSql .= "              , evento_base.cod_configuracao_base                                                               \n";
    $stSql .= "              , evento_base.timestamp_base) as evento_base                                                      \n";
    $stSql .= "     , folhapagamento.evento                                                                                    \n";
    $stSql .= " WHERE evento_base.cod_evento_base = evento.cod_evento                                                               \n";

    return $stSql;
}

function recuperaRelacionamentoPorCaso(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY descricao ";
    $stSql  = $this->montaRecuperaRelacionamentoPorCaso().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoPorCaso()
{
    $stSql .= "SELECT evento.*                                                                  \n";
    $stSql .= "  FROM folhapagamento.evento_base                                                \n";
    $stSql .= "     , folhapagamento.evento                                                     \n";
    $stSql .= "     , folhapagamento.configuracao_evento_caso as evento_caso_base               \n";
    $stSql .= "     , folhapagamento.configuracao_evento_caso as evento_caso                    \n";
    $stSql .= "     , (  SELECT cod_evento                                                      \n";
    $stSql .= "               , max(timestamp) as timestamp                                     \n";
    $stSql .= "            FROM folhapagamento.configuracao_evento_caso                         \n";
    $stSql .= "        GROUP BY cod_evento ) as max_evento_caso                                 \n";
    $stSql .= " WHERE                                                                           \n";
    $stSql .= "       evento.cod_evento             = evento_base.cod_evento_base               \n";
    $stSql .= "   AND evento_caso_base.cod_caso          = evento_base.cod_caso_base            \n";
    $stSql .= "   AND evento_caso_base.cod_evento        = evento_base.cod_evento_base          \n";
    $stSql .= "   AND evento_caso_base.cod_configuracao  = evento_base.cod_configuracao_base    \n";
    $stSql .= "   AND evento_caso_base.timestamp         = evento_base.timestamp_base           \n";
    $stSql .= "   AND evento_caso.cod_caso          = evento_base.cod_caso                      \n";
    $stSql .= "   AND evento_caso.cod_evento        = evento_base.cod_evento                    \n";
    $stSql .= "   AND evento_caso.cod_configuracao  = evento_base.cod_configuracao              \n";
    $stSql .= "   AND evento_caso.timestamp         = evento_base.timestamp                     \n";
    $stSql .= "   AND evento_caso.cod_evento        = max_evento_caso.cod_evento                \n";
    $stSql .= "   AND evento_caso.timestamp         = max_evento_caso.timestamp                 \n";

    return $stSql;
}

function recuperaRelacionamentoEventosDeEventosBase(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_evento_base ";
    $stSql  = $this->montaRecuperaRelacionamentoEventosDeEventosBase().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoEventosDeEventosBase()
{
    $stSql .= "SELECT evento_base.*                                                             \n";
    $stSql .= "  FROM folhapagamento.evento_base                                                \n";
    $stSql .= "     , folhapagamento.configuracao_evento_caso                                   \n";
    $stSql .= "     , folhapagamento.evento_configuracao_evento                                 \n";
    $stSql .= "     , folhapagamento.evento_evento                                              \n";
    $stSql .= "     , (  SELECT cod_evento                                                      \n";
    $stSql .= "               , max(timestamp) as timestamp                                     \n";
    $stSql .= "            FROM folhapagamento.evento_evento                                    \n";
    $stSql .= "        GROUP BY cod_evento ) as max_evento_evento                               \n";
    $stSql .= " WHERE evento_base.cod_evento       = configuracao_evento_caso.cod_evento        \n";
    $stSql .= "   AND evento_base.cod_caso         = configuracao_evento_caso.cod_caso          \n";
    $stSql .= "   AND evento_base.cod_configuracao = configuracao_evento_caso.cod_configuracao  \n";
    $stSql .= "   AND evento_base.timestamp        = configuracao_evento_caso.timestamp         \n";
    $stSql .= "   AND configuracao_evento_caso.cod_evento = evento_configuracao_evento.cod_evento \n";
    $stSql .= "   AND configuracao_evento_caso.timestamp  = evento_configuracao_evento.timestamp  \n";
    $stSql .= "   AND configuracao_evento_caso.cod_configuracao = evento_configuracao_evento.cod_configuracao \n";
    $stSql .= "   AND evento_configuracao_evento.cod_evento = evento_evento.cod_evento            \n";
    $stSql .= "   AND evento_evento.cod_evento = max_evento_evento.cod_evento                     \n";
    $stSql .= "   AND evento_evento.timestamp  = max_evento_evento.timestamp                      \n";
    $stSql .= "   AND evento_evento.timestamp  = evento_base.timestamp                            \n";

    return $stSql;
}

function recuperaEventoBase(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_evento_base ";
    $stSql  = $this->montaRecuperaEventoBase().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventoBase()
{
    $stSql .= "SELECT evento_base.cod_evento                                                               \n";
    $stSql .= "     , evento_base.cod_evento_base                                                          \n";
    $stSql .= "     , evento_base.cod_caso                                                                 \n";
    $stSql .= "     , evento_base.cod_configuracao                                                         \n";
    $stSql .= "     , TO_CHAR(evento_base.timestamp,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp               \n";
    $stSql .= "     , evento_base.cod_caso_base                                                            \n";
    $stSql .= "     , evento_base.cod_configuracao_base                                                    \n";
    $stSql .= "     , TO_CHAR(evento_base.timestamp_base,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp_base     \n";
    $stSql .= "     , trim(evento.descricao) as descricao_base                                             \n";
    $stSql .= "     , (evento.codigo) as codigo_base                                                       \n";
    $stSql .= "  FROM folhapagamento.evento_base                                                           \n";
    $stSql .= "     ,(  SELECT cod_evento_base                                                             \n";
    $stSql .= "              , cod_evento                                                                  \n";
    $stSql .= "              , cod_configuracao_base                                                       \n";
    $stSql .= "              , cod_configuracao                                                            \n";
    $stSql .= "              , max(timestamp_base) as timestamp_base                                       \n";
    $stSql .= "              , max(timestamp) as timestamp                                                 \n";
    $stSql .= "           FROM folhapagamento.evento_base                                                  \n";
    if ( $this->getDado("cod_configuracao_base") != "" ) {
        $stSql .= "      WHERE cod_configuracao_base = ".$this->getDado("cod_configuracao_base")."         \n";
    }
    $stSql .= "       GROUP BY cod_evento_base                                                             \n";
    $stSql .= "              , cod_evento                                                                  \n";
    $stSql .= "              , cod_configuracao_base                                                       \n";
    $stSql .= "              , cod_configuracao                                                            \n";
    $stSql .= "       ORDER BY cod_evento_base) as max_evento_base                                         \n";
    $stSql .= "     , folhapagamento.evento                                                                \n";
    $stSql .= " WHERE evento_base.cod_evento_base = max_evento_base.cod_evento_base                        \n";
    $stSql .= "   AND evento_base.timestamp_base  = max_evento_base.timestamp_base                         \n";
    $stSql .= "   AND evento_base.cod_evento = max_evento_base.cod_evento                                  \n";
    $stSql .= "   AND evento_base.timestamp  = max_evento_base.timestamp                                   \n";
    $stSql .= "   AND evento_base.cod_configuracao_base = max_evento_base.cod_configuracao_base            \n";
    $stSql .= "   AND evento_base.cod_configuracao      = max_evento_base.cod_configuracao                 \n";
    $stSql .= "   AND evento_base.cod_evento_base = evento.cod_evento                                      \n";

    return $stSql;
}

function recuperaEventoBaseDesdobramento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_evento_base ";
    $stSql  = $this->montaRecuperaEventoBaseDesdobramento().$stFiltro.$stOrdem;

    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventoBaseDesdobramento()
{
    $stSql .= "SELECT evento_base.cod_evento                                                               \n";
    $stSql .= "     , evento_base.cod_evento_base                                                          \n";
    $stSql .= "     , evento_base.cod_caso                                                                 \n";
    $stSql .= "     , evento_base.cod_configuracao                                                         \n";
    $stSql .= "     , TO_CHAR(evento_base.timestamp,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp               \n";
    $stSql .= "     , evento_base.cod_caso_base                                                            \n";
    $stSql .= "     , evento_base.cod_configuracao_base                                                    \n";
    $stSql .= "     , TO_CHAR(evento_base.timestamp_base,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp_base     \n";
    $stSql .= "     , trim(evento.descricao) as descricao_base                                             \n";
    $stSql .= "     , (evento.codigo) as codigo_base                                                       \n";
    $stSql .= "     , getDesdobramentoFerias(registro_evento_ferias.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto  \n";
    $stSql .= "  FROM folhapagamento.evento_base                                                           \n";
    $stSql .= "     ,(  SELECT cod_evento_base                                                             \n";
    $stSql .= "              , cod_evento                                                                  \n";
    $stSql .= "              , cod_configuracao_base                                                       \n";
    $stSql .= "              , cod_configuracao                                                            \n";
    $stSql .= "              , max(timestamp_base) as timestamp_base                                       \n";
    $stSql .= "              , max(timestamp) as timestamp                                                 \n";
    $stSql .= "           FROM folhapagamento.evento_base                                                  \n";
    if ( $this->getDado("cod_configuracao_base") != "" ) {
        $stSql .= "      WHERE cod_configuracao_base = ".$this->getDado("cod_configuracao_base")."         \n";
    }
    $stSql .= "       GROUP BY cod_evento_base                                                             \n";
    $stSql .= "              , cod_evento                                                                  \n";
    $stSql .= "              , cod_configuracao_base                                                       \n";
    $stSql .= "              , cod_configuracao                                                            \n";
    $stSql .= "       ORDER BY cod_evento_base) as max_evento_base                                         \n";
    $stSql .= "     , folhapagamento.evento                                                                \n";
    $stSql .= "     , folhapagamento.registro_evento_ferias                                                \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_ferias                                         \n";
    $stSql .= " WHERE evento_base.cod_evento_base = max_evento_base.cod_evento_base                        \n";
    $stSql .= "   AND evento_base.timestamp_base  = max_evento_base.timestamp_base                         \n";
    $stSql .= "   AND evento_base.cod_evento = max_evento_base.cod_evento                                  \n";
    $stSql .= "   AND evento_base.timestamp  = max_evento_base.timestamp                                   \n";
    $stSql .= "   AND evento_base.cod_configuracao_base = max_evento_base.cod_configuracao_base            \n";
    $stSql .= "   AND evento_base.cod_configuracao      = max_evento_base.cod_configuracao                 \n";
    $stSql .= "   AND evento_base.cod_evento_base       = evento.cod_evento                                \n";
    $stSql .= "   AND registro_evento_ferias.cod_evento    = ultimo_registro_evento_ferias.cod_evento      \n";
    $stSql .= "   AND registro_evento_ferias.cod_registro  = ultimo_registro_evento_ferias.cod_registro    \n";
    $stSql .= "   AND registro_evento_ferias.timestamp     = ultimo_registro_evento_ferias.timestamp       \n";
    $stSql .= "   AND registro_evento_ferias.desdobramento = ultimo_registro_evento_ferias.desdobramento   \n";
    $stSql .= "   AND registro_evento_ferias.cod_evento = evento.cod_evento                                \n";

    return $stSql;
}

function recuperaEventoBaseDesdobramentoDecimo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_evento_base ";
    $stSql  = $this->montaRecuperaEventoBaseDesdobramentoDecimo().$stFiltro.$stOrdem;

    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventoBaseDesdobramentoDecimo()
{
    $stSql .= "SELECT evento_base.cod_evento                                                               \n";
    $stSql .= "     , evento_base.cod_evento_base                                                          \n";
    $stSql .= "     , evento_base.cod_caso                                                                 \n";
    $stSql .= "     , evento_base.cod_configuracao                                                         \n";
    $stSql .= "     , TO_CHAR(evento_base.timestamp,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp               \n";
    $stSql .= "     , evento_base.cod_caso_base                                                            \n";
    $stSql .= "     , evento_base.cod_configuracao_base                                                    \n";
    $stSql .= "     , TO_CHAR(evento_base.timestamp_base,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp_base     \n";
    $stSql .= "     , trim(evento.descricao) as descricao_base                                             \n";
    $stSql .= "     , (evento.codigo) as codigo_base                                                       \n";
    $stSql .= "     , getDesdobramentoDecimo(registro_evento_Decimo.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto  \n";
    $stSql .= "  FROM folhapagamento.evento_base                                                           \n";
    $stSql .= "     ,(  SELECT cod_evento_base                                                             \n";
    $stSql .= "              , cod_evento                                                                  \n";
    $stSql .= "              , cod_configuracao_base                                                       \n";
    $stSql .= "              , cod_configuracao                                                            \n";
    $stSql .= "              , max(timestamp_base) as timestamp_base                                       \n";
    $stSql .= "              , max(timestamp) as timestamp                                                 \n";
    $stSql .= "           FROM folhapagamento.evento_base                                                  \n";
    if ( $this->getDado("cod_configuracao_base") != "" ) {
        $stSql .= "      WHERE cod_configuracao_base = ".$this->getDado("cod_configuracao_base")."         \n";
    }
    $stSql .= "       GROUP BY cod_evento_base                                                             \n";
    $stSql .= "              , cod_evento                                                                  \n";
    $stSql .= "              , cod_configuracao_base                                                       \n";
    $stSql .= "              , cod_configuracao                                                            \n";
    $stSql .= "       ORDER BY cod_evento_base) as max_evento_base                                         \n";
    $stSql .= "     , folhapagamento.evento                                                                \n";
    $stSql .= "     , folhapagamento.registro_evento_Decimo                                                \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_Decimo                                         \n";
    $stSql .= " WHERE evento_base.cod_evento_base = max_evento_base.cod_evento_base                        \n";
    $stSql .= "   AND evento_base.timestamp_base  = max_evento_base.timestamp_base                         \n";
    $stSql .= "   AND evento_base.cod_evento = max_evento_base.cod_evento                                  \n";
    $stSql .= "   AND evento_base.timestamp  = max_evento_base.timestamp                                   \n";
    $stSql .= "   AND evento_base.cod_configuracao_base = max_evento_base.cod_configuracao_base            \n";
    $stSql .= "   AND evento_base.cod_configuracao      = max_evento_base.cod_configuracao                 \n";
    $stSql .= "   AND evento_base.cod_evento_base       = evento.cod_evento                                \n";
    $stSql .= "   AND registro_evento_Decimo.cod_evento    = ultimo_registro_evento_Decimo.cod_evento      \n";
    $stSql .= "   AND registro_evento_Decimo.cod_registro  = ultimo_registro_evento_Decimo.cod_registro    \n";
    $stSql .= "   AND registro_evento_Decimo.timestamp     = ultimo_registro_evento_Decimo.timestamp       \n";
    $stSql .= "   AND registro_evento_Decimo.desdobramento = ultimo_registro_evento_Decimo.desdobramento   \n";
    $stSql .= "   AND registro_evento_Decimo.cod_evento = evento_base.cod_evento                                \n";

    return $stSql;
}

function recuperaEventoBaseDesdobramentoRescisao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_evento_base ";
    $stSql  = $this->montaRecuperaEventoBaseDesdobramentoRescisao().$stFiltro.$stOrdem;

    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventoBaseDesdobramentoRescisao()
{
    $stSql .= "SELECT evento_base.cod_evento                                                               \n";
    $stSql .= "     , evento_base.cod_evento_base                                                          \n";
    $stSql .= "     , evento_base.cod_caso                                                                 \n";
    $stSql .= "     , evento_base.cod_configuracao                                                         \n";
    $stSql .= "     , TO_CHAR(evento_base.timestamp,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp               \n";
    $stSql .= "     , evento_base.cod_caso_base                                                            \n";
    $stSql .= "     , evento_base.cod_configuracao_base                                                    \n";
    $stSql .= "     , TO_CHAR(evento_base.timestamp_base,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp_base     \n";
    $stSql .= "     , trim(evento.descricao) as descricao_base                                             \n";
    $stSql .= "     , (evento.codigo) as codigo_base                                                       \n";
    $stSql .= "     , getDesdobramentoRescisao(registro_evento_rescisao.desdobramento,'".Sessao::getEntidade()."') as desdobramento_texto \n";
    $stSql .= "  FROM folhapagamento.evento_base                                                           \n";
    $stSql .= "     ,(  SELECT cod_evento_base                                                             \n";
    $stSql .= "              , cod_evento                                                                  \n";
    $stSql .= "              , cod_configuracao_base                                                       \n";
    $stSql .= "              , cod_configuracao                                                            \n";
    $stSql .= "              , max(timestamp_base) as timestamp_base                                       \n";
    $stSql .= "              , max(timestamp) as timestamp                                                 \n";
    $stSql .= "           FROM folhapagamento.evento_base                                                  \n";
    if ( $this->getDado("cod_configuracao_base") != "" ) {
        $stSql .= "      WHERE cod_configuracao_base = ".$this->getDado("cod_configuracao_base")."         \n";
    }
    $stSql .= "       GROUP BY cod_evento_base                                                             \n";
    $stSql .= "              , cod_evento                                                                  \n";
    $stSql .= "              , cod_configuracao_base                                                       \n";
    $stSql .= "              , cod_configuracao                                                            \n";
    $stSql .= "       ORDER BY cod_evento_base) as max_evento_base                                         \n";
    $stSql .= "     , folhapagamento.evento                                                                \n";
    $stSql .= "     , folhapagamento.registro_evento_rescisao                                                \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento_rescisao                                         \n";
    $stSql .= " WHERE evento_base.cod_evento_base = max_evento_base.cod_evento_base                        \n";
    $stSql .= "   AND evento_base.timestamp_base  = max_evento_base.timestamp_base                         \n";
    $stSql .= "   AND evento_base.cod_evento = max_evento_base.cod_evento                                  \n";
    $stSql .= "   AND evento_base.timestamp  = max_evento_base.timestamp                                   \n";
    $stSql .= "   AND evento_base.cod_configuracao_base = max_evento_base.cod_configuracao_base            \n";
    $stSql .= "   AND evento_base.cod_configuracao      = max_evento_base.cod_configuracao                 \n";
    $stSql .= "   AND evento_base.cod_evento_base       = evento.cod_evento                                \n";
    $stSql .= "   AND registro_evento_rescisao.cod_evento    = ultimo_registro_evento_rescisao.cod_evento      \n";
    $stSql .= "   AND registro_evento_rescisao.cod_registro  = ultimo_registro_evento_rescisao.cod_registro    \n";
    $stSql .= "   AND registro_evento_rescisao.timestamp     = ultimo_registro_evento_rescisao.timestamp       \n";
    $stSql .= "   AND registro_evento_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento   \n";
    $stSql .= "   AND registro_evento_rescisao.cod_evento = evento_base.cod_evento                                \n";

    return $stSql;
}

}
