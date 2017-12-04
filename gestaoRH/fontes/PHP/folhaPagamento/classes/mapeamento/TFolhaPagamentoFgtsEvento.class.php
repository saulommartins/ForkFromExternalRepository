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
    * Classe de mapeamento da tabela folhapagamento.fgts_evento
    * Data de Criação: 10/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: alex $
    $Date: 2008-03-12 16:23:42 -0300 (Qua, 12 Mar 2008) $

    * Casos de uso: uc-04.05.42
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.fgts_evento
  * Data de Criação: 10/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoFgtsEvento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoFgtsEvento()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.fgts_evento");

    $this->setCampoCod('');
    $this->setComplementoChave('timestamp,cod_tipo,cod_fgts');

    $this->AddCampo('timestamp','timestamp',false,'',true,true);
    $this->AddCampo('cod_tipo','integer',true,'',true,true);
    $this->AddCampo('cod_fgts','integer',true,'',true,true);
    $this->AddCampo('cod_evento','integer',true,'',false,true);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT fgts_evento.*                                         \n";
    $stSql .= "     , trim(evento.descricao) as descricao                                      \n";
    $stSql .= "     , evento.codigo                                         \n";
    $stSql .= "  FROM folhapagamento.fgts_evento                            \n";
    $stSql .= "     , (  SELECT cod_fgts                                    \n";
    $stSql .= "               , max(timestamp) as timestamp                 \n";
    $stSql .= "            FROM folhapagamento.fgts_evento                  \n";
    $stSql .= "        GROUP BY cod_fgts) as max_fgts_evento                \n";
    $stSql .= "     , folhapagamento.evento                                 \n";
    $stSql .= " WHERE fgts_evento.cod_evento = evento.cod_evento            \n";
    $stSql .= "   AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts     \n";
    $stSql .= "   AND fgts_evento.timestamp  = max_fgts_evento.timestamp    \n";

    return $stSql;
}

function recuperaEventoCalculadoFgts(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaEventoCalculadoFgts().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEventoCalculadoFgts()
{
    $stSql .= "SELECT *                                                                                                                \n";
    $stSql .= "  FROM (SELECT sum(evento_calculado.valor) as valor                                                                 \n";
    $stSql .= "             , contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_periodo.cod_periodo_movimentacao                                                         \n";
    $stSql .= "             , 1 AS inFolha                                                                                             \n";
    $stSql .= "        FROM folhapagamento.evento_calculado                                                                            \n";
    $stSql .= "             , folhapagamento.registro_evento                                                                           \n";
    $stSql .= "             , folhapagamento.registro_evento_periodo                                                                   \n";
    $stSql .= "             , pessoal.contrato                                                                                         \n";
    $stSql .= "             , folhapagamento.fgts_evento                                                                               \n";
    $stSql .= "             , (SELECT cod_fgts                                                                                         \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                      \n";
    $stSql .= "                  FROM folhapagamento.fgts_evento                                                                       \n";
    $stSql .= "                GROUP BY cod_fgts) as max_fgts_evento                                                                   \n";
    $stSql .= "         WHERE evento_calculado.cod_evento = registro_evento.cod_evento                                                 \n";
    $stSql .= "           AND evento_calculado.cod_registro = registro_evento.cod_registro                                             \n";
    $stSql .= "           AND evento_calculado.timestamp_registro = registro_evento.timestamp                                          \n";
    $stSql .= "           AND registro_evento.cod_registro = registro_evento_periodo.cod_registro                                      \n";
    $stSql .= "           AND registro_evento_periodo.cod_contrato = contrato.cod_contrato                                             \n";
    $stSql .= "           AND registro_evento.cod_evento = fgts_evento.cod_evento                                                      \n";
    $stSql .= "           AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                          \n";
    $stSql .= "           AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                        \n";
    $stSql .= "           AND fgts_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                                                                 \n";
    $stSql .= "           AND (desdobramento IS NULL OR desdobramento = 'A' OR desdobramento = 'F')                                                                                       \n";
    $stSql .= "      GROUP BY contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_periodo.cod_periodo_movimentacao                                                         \n";
    $stSql .= "         UNION                                                                                                          \n";
    $stSql .= "        SELECT evento_ferias_calculado.valor as valor                                                          \n";
    $stSql .= "             , contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_ferias.cod_periodo_movimentacao                                                          \n";
    $stSql .= "             , 2 AS inFolha                                                                                             \n";
    $stSql .= "          FROM folhapagamento.evento_ferias_calculado                                                                   \n";
    $stSql .= "             , folhapagamento.registro_evento_ferias                                                                    \n";
    $stSql .= "             , pessoal.contrato                                                                                         \n";
    $stSql .= "             , folhapagamento.fgts_evento                                                                               \n";
    $stSql .= "             , (SELECT cod_fgts                                                                                         \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                      \n";
    $stSql .= "                  FROM folhapagamento.fgts_evento                                                                       \n";
    $stSql .= "                GROUP BY cod_fgts) as max_fgts_evento                                                                   \n";
    $stSql .= "         WHERE evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento                           \n";
    $stSql .= "           AND evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro                         \n";
    $stSql .= "           AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento                        \n";
    $stSql .= "           AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp                            \n";
    $stSql .= "           AND registro_evento_ferias.cod_contrato = contrato.cod_contrato                                              \n";
    $stSql .= "           AND registro_evento_ferias.cod_evento = fgts_evento.cod_evento                                               \n";
    $stSql .= "           AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                          \n";
    $stSql .= "           AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                        \n";
    $stSql .= "           AND fgts_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                                                                 \n";

    if ($this->getDado("desdobramento") != "") {
        $stSql .= "           AND evento_ferias_calculado.desdobramento = '".$this->getDado("desdobramento")."'                                                                                       \n";
    }

    $stSql .= "         UNION                                                                                                          \n";
    $stSql .= "        SELECT evento_decimo_calculado.valor as valor                                                          \n";
    $stSql .= "             , contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_decimo.cod_periodo_movimentacao                                                          \n";
    $stSql .= "             , 3 AS inFolha                                                                                             \n";
    $stSql .= "          FROM folhapagamento.evento_decimo_calculado                                                                   \n";
    $stSql .= "             , folhapagamento.registro_evento_decimo                                                                    \n";
    $stSql .= "             , pessoal.contrato                                                                                         \n";
    $stSql .= "             , folhapagamento.fgts_evento                                                                               \n";
    $stSql .= "             , (SELECT cod_fgts                                                                                         \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                      \n";
    $stSql .= "                  FROM folhapagamento.fgts_evento                                                                       \n";
    $stSql .= "                GROUP BY cod_fgts) as max_fgts_evento                                                                   \n";
    $stSql .= "         WHERE evento_decimo_calculado.cod_evento         = registro_evento_decimo.cod_evento                           \n";
    $stSql .= "           AND evento_decimo_calculado.cod_registro       = registro_evento_decimo.cod_registro                         \n";
    $stSql .= "           AND evento_decimo_calculado.desdobramento      = registro_evento_decimo.desdobramento                        \n";
    $stSql .= "           AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp                            \n";
    $stSql .= "           AND registro_evento_decimo.cod_contrato = contrato.cod_contrato                                              \n";
    $stSql .= "           AND registro_evento_decimo.cod_evento = fgts_evento.cod_evento                                               \n";
    $stSql .= "           AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                          \n";
    $stSql .= "           AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                        \n";
    $stSql .= "           AND fgts_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                                                                 \n";

    if ($this->getDado("desdobramento") != "") {
        $stSql .= "           AND evento_decimo_calculado.desdobramento = '".$this->getDado("desdobramento")."'                                                                                       \n";
    }

    $stSql .= "        UNION                                                                                                           \n";
    $stSql .= "        SELECT evento_rescisao_calculado.valor as valor                                                        \n";
    $stSql .= "             , contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_rescisao.cod_periodo_movimentacao                                                        \n";
    $stSql .= "             , 4 AS inFolha                                                                                             \n";
    $stSql .= "          FROM folhapagamento.evento_rescisao_calculado                                                                 \n";
    $stSql .= "             , folhapagamento.registro_evento_rescisao                                                                  \n";
    $stSql .= "             , pessoal.contrato                                                                                         \n";
    $stSql .= "             , folhapagamento.fgts_evento                                                                               \n";
    $stSql .= "             , (SELECT cod_fgts                                                                                         \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                      \n";
    $stSql .= "                  FROM folhapagamento.fgts_evento                                                                       \n";
    $stSql .= "                GROUP BY cod_fgts) as max_fgts_evento                                                                   \n";
    $stSql .= "         WHERE evento_rescisao_calculado.cod_evento         = registro_evento_rescisao.cod_evento                       \n";
    $stSql .= "           AND evento_rescisao_calculado.cod_registro       = registro_evento_rescisao.cod_registro                     \n";
    $stSql .= "           AND evento_rescisao_calculado.desdobramento      = registro_evento_rescisao.desdobramento                    \n";
    $stSql .= "           AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp                        \n";
    $stSql .= "           AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato                                            \n";
    $stSql .= "           AND registro_evento_rescisao.cod_evento = fgts_evento.cod_evento                                             \n";
    $stSql .= "           AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                          \n";
    $stSql .= "           AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                        \n";
    $stSql .= "           AND fgts_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                                                                 \n";

    if ($this->getDado("desdobramento") != "") {
        $stSql .= "           AND evento_rescisao_calculado.desdobramento = '".$this->getDado("desdobramento")."'                                                                                       \n";
    }

    $stSql .= "        UNION                                                                                                           \n";
    $stSql .= "        SELECT sum(evento_complementar_calculado.valor) as valor                                                    \n";
    $stSql .= "             , contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_complementar.cod_periodo_movimentacao                                                    \n";
    $stSql .= "             , 0 AS inFolha                                                                                             \n";
    $stSql .= "          FROM folhapagamento.evento_complementar_calculado                                   \n";
    $stSql .= "             , folhapagamento.registro_evento_complementar                                    \n";
    $stSql .= "             , pessoal.contrato                                                               \n";
    $stSql .= "             , folhapagamento.fgts_evento                                                     \n";
    $stSql .= "             , (SELECT cod_fgts                                                                                         \n";
    $stSql .= "                     , max(timestamp) as timestamp                                                                      \n";
    $stSql .= "                  FROM folhapagamento.fgts_evento                                             \n";
    $stSql .= "                GROUP BY cod_fgts) as max_fgts_evento                                                                   \n";
    $stSql .= "         WHERE evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento               \n";
    $stSql .= "           AND evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro             \n";
    $stSql .= "           AND evento_complementar_calculado.cod_configuracao   = registro_evento_complementar.cod_configuracao         \n";
    $stSql .= "           AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp                \n";
    $stSql .= "           AND registro_evento_complementar.cod_contrato = contrato.cod_contrato                                        \n";
    $stSql .= "           AND registro_evento_complementar.cod_evento = fgts_evento.cod_evento                                         \n";
    $stSql .= "           AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                          \n";
    $stSql .= "           AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                        \n";
    $stSql .= "           AND registro_evento_complementar.cod_complementar = ".$this->getDado("cod_complementar")."                   \n";
    $stSql .= "           AND (desdobramento IS NULL OR desdobramento = 'A' OR desdobramento = 'F')                                    \n";
    $stSql .= "           AND fgts_evento.cod_tipo = ".$this->getDado("cod_tipo")."                                                    \n";
    $stSql .= "      GROUP BY contrato.cod_contrato                                                                                    \n";
    $stSql .= "             , registro_evento_complementar.cod_periodo_movimentacao) as fgts                                                \n";

    return $stSql;
}

}
