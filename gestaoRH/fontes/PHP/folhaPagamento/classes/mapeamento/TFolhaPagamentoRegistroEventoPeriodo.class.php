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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.REGISTRO_EVENTO_PERIODO
    * Data de Criação: 04/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-04-03 10:54:36 -0300 (Qui, 03 Abr 2008) $

    * Casos de uso: uc-04.05.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.REGISTRO_EVENTO_PERIODO
  * Data de Criação: 04/11/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoRegistroEventoPeriodo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoRegistroEventoPeriodo()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.registro_evento_periodo');

    $this->setCampoCod('cod_registro');
    $this->setComplementoChave('');

    $this->AddCampo( 'cod_registro'             , 'sequence', true, '', true , false                                    );
    $this->AddCampo( 'cod_contrato'             , 'integer', true, '', false, 'TFolhaPagamentoContratoServidorPeriodo' );
    $this->AddCampo( 'cod_periodo_movimentacao' , 'integer', true, '', false, 'TFolhaPagamentoContratoServidorPeriodo' );
}

function recuperaContratosDeLotacao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY nom_cgm";
    $stSql  = $this->montaRecuperaContratosDeLotacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosDeLotacao()
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
    $stSql .= "     , folhapagamento.registro_evento_periodo                                         \n";
    $stSql .= "     , sw_cgm                                                                                                   \n";
    $stSql .= " WHERE servidor_pensionista.cod_contrato = contrato.cod_contrato                                                \n";
    $stSql .= "   AND contrato.cod_contrato = registro_evento_periodo.cod_contrato                                             \n";
    $stSql .= "   AND servidor_pensionista.numcgm = sw_cgm.numcgm                                                              \n";
    $stSql .= "   AND contrato.cod_contrato NOT IN (SELECT cod_contrato                                                        \n";
    $stSql .= "                                       FROM pessoal.contrato_servidor_caso_causa )    \n";
    $stSql .= "   AND cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."                                \n";
    $stSql .= "   AND cod_orgao IN (".$this->getDado("cod_orgao").")                                                           \n";
    $stSql .= "GROUP BY contrato.registro                                                                                      \n";
    $stSql .= "       , contrato.cod_contrato                                                                                  \n";
    $stSql .= "       , sw_cgm.numcgm                                                                                          \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                                         \n";

    return $stSql;
}

function recuperaContratosDeLocal(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY nom_cgm";
    $stSql  = $this->montaRecuperaContratosDeLocal().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosDeLocal()
{
    $stSql .= "SELECT contrato.*                                                                                               \n";
    $stSql .= "     , sw_cgm.numcgm                                                                                            \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                           \n";
    $stSql .= "  FROM (SELECT contrato_servidor_local.cod_contrato                                                             \n";
    $stSql .= "             , contrato_servidor_local.cod_local                                                                \n";
    $stSql .= "             , numcgm                                                                                           \n";
    $stSql .= "          FROM pessoal.contrato_servidor_local                                         \n";
    $stSql .= "             , (  SELECT cod_contrato                                                                           \n";
    $stSql .= "                       , MAX(timestamp) as timestamp                                                            \n";
    $stSql .= "                    FROM pessoal.contrato_servidor_local                               \n";
    $stSql .= "                GROUP BY cod_contrato) as max_contrato_servidor_local                                           \n";
    $stSql .= "             , pessoal.servidor_contrato_servidor                                      \n";
    $stSql .= "             , pessoal.servidor                                                        \n";
    $stSql .= "         WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                  \n";
    $stSql .= "           AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp                        \n";
    $stSql .= "           AND contrato_servidor_local.cod_contrato = servidor_contrato_servidor.cod_contrato                   \n";
    $stSql .= "           AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                  \n";
    $stSql .= "         UNION                                                                                                  \n";
    $stSql .= "        SELECT contrato_pensionista.cod_contrato                                                                \n";
    $stSql .= "             , contrato_servidor_local.cod_local                                                                \n";
    $stSql .= "             , numcgm                                                                                           \n";
    $stSql .= "          FROM pessoal.contrato_servidor_local                                         \n";
    $stSql .= "             , (  SELECT cod_contrato                                                                           \n";
    $stSql .= "                       , MAX(timestamp) as timestamp                                                            \n";
    $stSql .= "                    FROM pessoal.contrato_servidor_local                               \n";
    $stSql .= "                GROUP BY cod_contrato) as max_contrato_servidor_local                                           \n";
    $stSql .= "             , pessoal.contrato_pensionista                                            \n";
    $stSql .= "             , pessoal.pensionista                                                     \n";
    $stSql .= "         WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                  \n";
    $stSql .= "           AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp                        \n";
    $stSql .= "           AND contrato_servidor_local.cod_contrato = contrato_pensionista.cod_contrato_cedente                 \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                     \n";
    $stSql .= "           AND contrato_pensionista.cod_pensionista = pensionista.cod_pensionista) as servidor_pensionista      \n";
    $stSql .= "     , pessoal.contrato                                                                \n";
    $stSql .= "     , folhapagamento.registro_evento_periodo                                          \n";
    $stSql .= "     , sw_cgm                                                                                                   \n";
    $stSql .= " WHERE servidor_pensionista.cod_contrato = contrato.cod_contrato                                                \n";
    $stSql .= "   AND contrato.cod_contrato = registro_evento_periodo.cod_contrato                                             \n";
    $stSql .= "   AND servidor_pensionista.numcgm = sw_cgm.numcgm                                                              \n";
    $stSql .= "   AND contrato.cod_contrato NOT IN (SELECT cod_contrato                                                        \n";
    $stSql .= "                                       FROM pessoal.contrato_servidor_caso_causa )     \n";
    $stSql .= "   AND cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."                                \n";
    $stSql .= "   AND cod_local IN (".$this->getDado("cod_local").")                                                           \n";
    $stSql .= "GROUP BY contrato.registro                                                                                      \n";
    $stSql .= "       , contrato.cod_contrato                                                                                  \n";
    $stSql .= "       , sw_cgm.numcgm                                                                                          \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                                         \n";
    return $stSql;
}

function recuperarRegistroContratoSalario(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperarRegistroContratoSalario().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperarRegistroContratoSalarioComPensionista(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperarRegistroContratoSalarioComPensionista($stFiltro,$stOrdem);
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperarRegistroContratoSalario()
{
    $stSql .= "SELECT to_real(registro_evento.valor) as valor                                                                                                     \n";
    $stSql .= "     , to_real(registro_evento.quantidade) as quantidade                                                                                           \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_periodo.cod_contrato) as matricula     \n";
    $stSql .= "     , registro_evento_periodo.cod_contrato                                                                                                         \n";
    $stSql .= "     , servidor.numcgm                                                                                                                              \n";
    $stSql .= "     , registro_evento.proporcional                                                                                                                 \n";
    $stSql .= "     , registro_evento_periodo.cod_periodo_movimentacao                                                                                             \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                       \n";
    $stSql .= " FROM folhapagamento.registro_evento_periodo                                                                              \n";
    $stSql .= "     , folhapagamento.registro_evento                                                                                     \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento                                                                              \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                 \n";
    $stSql .= "     , pessoal.servidor                                                                                                   \n";
    $stSql .= " WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro                                                                          \n";
    $stSql .= "     AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro                                                                         \n";
    $stSql .= "     AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento                                                                             \n";
    $stSql .= "     AND registro_evento.timestamp = ultimo_registro_evento.timestamp                                                                               \n";
    $stSql .= "     AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato                                                             \n";
    $stSql .= "     AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                            \n";
    $stSql .= "     AND NOT EXISTS ( SELECT 1                                                                                                                       \n ";
    $stSql .= "                      FROM pessoal.contrato_servidor_caso_causa                                                            \n";
    $stSql .= "                      WHERE contrato_servidor_caso_causa.cod_contrato = servidor_contrato_servidor.cod_contrato)                                     \n";

    return $stSql;
}

function montaRecuperarRegistroContratoSalarioComPensionista($stFiltro,$stOrdem)
{
    $stSql .= "SELECT to_real(registro_evento.valor) as valor                                                                                                     \n";
    $stSql .= "     , to_real(registro_evento.quantidade) as quantidade                                                                                           \n";
    $stSql .= "     , (select registro from pessoal.contrato where cod_contrato = registro_evento_periodo.cod_contrato) as matricula     \n";
    $stSql .= "     , registro_evento_periodo.cod_contrato                                                                                                         \n";
    $stSql .= "     , servidor.numcgm                                                                                                                              \n";
    $stSql .= "     , registro_evento.proporcional                                                                                                                 \n";
    $stSql .= "     , registro_evento_periodo.cod_periodo_movimentacao                                                                                             \n";
    $stSql .= "     , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm                                                                       \n";
    $stSql .= " FROM folhapagamento.registro_evento_periodo                                                                              \n";
    $stSql .= "     , folhapagamento.registro_evento                                                                                     \n";
    $stSql .= "     , folhapagamento.ultimo_registro_evento                                                                              \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                 \n";
    $stSql .= "     , pessoal.servidor                                                                                                   \n";
    $stSql .= " WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro                                                                          \n";
    $stSql .= "     AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro                                                                         \n";
    $stSql .= "     AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento                                                                             \n";
    $stSql .= "     AND registro_evento.timestamp = ultimo_registro_evento.timestamp                                                                               \n";
    $stSql .= "     AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato                                                             \n";
    $stSql .= "     AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                            \n";
    $stSql .= "     AND NOT EXISTS ( SELECT 1                                                                                                                       \n ";
    $stSql .= "                      FROM pessoal.contrato_servidor_caso_causa                                                            \n";
    $stSql .= "                      WHERE contrato_servidor_caso_causa.cod_contrato = servidor_contrato_servidor.cod_contrato)                                     \n";
    $stSql .= $stFiltro;
    $stSql .= "UNION                                                                                                                                                \n";
    $stSql .= "  SELECT to_real(registro_evento.valor) as valor                                                                                                     \n";
    $stSql .= "      , to_real(registro_evento.quantidade) as quantidade                                                                                            \n";
    $stSql .= "      , (select registro from pessoal.contrato where cod_contrato = registro_evento_periodo.cod_contrato) as matricula                               \n";
    $stSql .= "      , registro_evento_periodo.cod_contrato                                                                                                         \n";
    $stSql .= "      , pensionista.numcgm                                                                                                                           \n";
    $stSql .= "      , registro_evento.proporcional                                                                                                                 \n";
    $stSql .= "      , registro_evento_periodo.cod_periodo_movimentacao                                                                                             \n";
    $stSql .= "      , (select nom_cgm from sw_cgm where numcgm = pensionista.numcgm) as nom_cgm                                                                    \n";
    $stSql .= "  FROM folhapagamento.registro_evento_periodo                                                                                                        \n";
    $stSql .= " INNER JOIN folhapagamento.registro_evento                                                                                                           \n";
    $stSql .= "         ON registro_evento_periodo.cod_registro = registro_evento.cod_registro                                                                      \n";
    $stSql .= " INNER JOIN folhapagamento.ultimo_registro_evento                                                                                                    \n";
    $stSql .= "         ON registro_evento.cod_registro = ultimo_registro_evento.cod_registro                                                                       \n";
    $stSql .= "        AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento                                                                           \n";
    $stSql .= "        AND registro_evento.timestamp = ultimo_registro_evento.timestamp                                                                             \n";
    $stSql .= " INNER JOIN pessoal.contrato_pensionista                                                                                                             \n";
    $stSql .= "         ON registro_evento_periodo.cod_contrato = contrato_pensionista.cod_contrato                                                                 \n";
    $stSql .= " INNER JOIN pessoal.pensionista                                                                                                                      \n";
    $stSql .= "         ON pensionista.cod_pensionista = contrato_pensionista.cod_pensionista                                                                       \n";
    $stSql .= "        AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente                                                             \n";
    $stSql .= "      AND NOT EXISTS ( SELECT 1                                                                                                                      \n";
    $stSql .= "                        FROM pessoal.contrato_servidor_caso_causa                                                                                    \n";
    $stSql .= "                       WHERE contrato_servidor_caso_causa.cod_contrato = contrato_pensionista.cod_contrato)                                          \n";
    $stSql .= $stFiltro;
    $stSql .= $stOrdem;

    return $stSql;
}

function deletarRegistroEventoPeriodo($boTransacao="")
{
    return $this->executaRecupera("montaDeletarRegistroPeriodo", $rsRecordSet, "", "", $boTransacao);
}

function montaDeletarRegistroPeriodo()
{
    $stSql  = "SELECT criarBufferTexto('stEntidade','".Sessao::getEntidade()."');       \n";
    $stSql .= "SELECT criarBufferTexto('stTipoFolha','S');                              \n";
    $stSql .= "SELECT deletarRegistroEventoPeriodo(".$this->getDado("cod_registro")."); \n";

    return $stSql;
}

function recuperaContratoGeral (&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaContratoGeral().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratoGeral()
{
    $stSql = "
                SELECT *

                  FROM (
                        SELECT
                                contrato.*
                                , servidor.numcgm                                                                                                   
                                , (select nom_cgm from sw_cgm where numcgm = servidor.numcgm) as nom_cgm

                          FROM folhapagamento.registro_evento_periodo

                    INNER JOIN folhapagamento.registro_evento
                            ON registro_evento_periodo.cod_registro = registro_evento.cod_registro

                    INNER JOIN folhapagamento.ultimo_registro_evento
                            ON registro_evento.cod_registro = ultimo_registro_evento.cod_registro
                           AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento
                           AND registro_evento.timestamp = ultimo_registro_evento.timestamp

                    INNER JOIN folhapagamento.contrato_servidor_periodo
                            ON contrato_servidor_periodo.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao
                           AND contrato_servidor_periodo.cod_contrato = registro_evento_periodo.cod_contrato

                    INNER JOIN pessoal.contrato
                            ON contrato.cod_contrato = contrato_servidor_periodo.cod_contrato

                    INNER JOIN pessoal.contrato_servidor
                            ON contrato_servidor.cod_contrato = contrato.cod_contrato

                    INNER JOIN pessoal.servidor_contrato_servidor
                            ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato

                    INNER JOIN pessoal.servidor
                            ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                         WHERE NOT EXISTS ( SELECT 1                                                                                            
                                              FROM pessoal.contrato_servidor_caso_causa                                                           
                                             WHERE contrato_servidor_caso_causa.cod_contrato = servidor_contrato_servidor.cod_contrato
                                          )

                         UNION

                        SELECT contrato.*
                             , pensionista.numcgm
                             , (select nom_cgm from sw_cgm where numcgm = pensionista.numcgm) as nom_cgm

                          FROM folhapagamento.registro_evento_periodo

                    INNER JOIN folhapagamento.registro_evento
                            ON registro_evento_periodo.cod_registro = registro_evento.cod_registro

                    INNER JOIN folhapagamento.ultimo_registro_evento
                            ON registro_evento.cod_registro = ultimo_registro_evento.cod_registro
                           AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento
                           AND registro_evento.timestamp = ultimo_registro_evento.timestamp

                    INNER JOIN pessoal.contrato_pensionista
                            ON registro_evento_periodo.cod_contrato = contrato_pensionista.cod_contrato

                    INNER JOIN pessoal.contrato
                            ON contrato.cod_contrato = contrato_pensionista.cod_contrato

                    INNER JOIN pessoal.pensionista
                            ON pensionista.cod_pensionista = contrato_pensionista.cod_pensionista
                           AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente
                           AND NOT EXISTS ( SELECT 1
                                                FROM pessoal.contrato_servidor_caso_causa
                                               WHERE contrato_servidor_caso_causa.cod_contrato = contrato_pensionista.cod_contrato)
                        ) AS tabela
            ";

    return $stSql;
}

}
