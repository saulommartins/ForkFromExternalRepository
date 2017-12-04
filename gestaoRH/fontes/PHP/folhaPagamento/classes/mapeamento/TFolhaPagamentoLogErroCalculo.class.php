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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.LOG_ERRO_CALCULO
    * Data de Criação: 07/12/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2008-01-09 16:03:59 -0200 (Qua, 09 Jan 2008) $

    * Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.LOG_ERRO_CALCULO
  * Data de Criação: 07/12/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoLogErroCalculo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoLogErroCalculo()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.log_erro_calculo');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_registro,cod_evento,timestamp');
    $this->AddCampo('cod_registro','integer',true,'',true       ,'TFolhaPagamentoUltimoRegistroEvento');
    $this->AddCampo('cod_evento','integer',true,'',true         ,'TFolhaPagamentoUltimoRegistroEvento');
    $this->AddCampo('timestamp','timestamp',false,'',true        ,'TFolhaPagamentoUltimoRegistroEvento');
    $this->AddCampo('erro','varchar',true,'250',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT log.*                                                                                                        \n";
    $stSql .= "     , contrato.*                                                                                                   \n";
    $stSql .= "     , sw_cgm.numcgm                                                                                                \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                               \n";
    $stSql .= "     , evento.codigo                                                                                                \n";
    $stSql .= "  FROM folhapagamento.log_erro_calculo as log                                                                       \n";
    $stSql .= "     , folhapagamento.registro_evento                                                                               \n";
    $stSql .= "     , folhapagamento.registro_evento_periodo                                                                       \n";
    $stSql .= "     , folhapagamento.contrato_servidor_periodo                                                                     \n";
    $stSql .= "     , pessoal.contrato                                                                                             \n";
    $stSql .= "     , (SELECT contrato_pensionista.cod_contrato                                                                    \n";
    $stSql .= "             , pensionista.numcgm                                                                                   \n";
    $stSql .= "          FROM pessoal.contrato_pensionista                                                                         \n";
    $stSql .= "             , pessoal.pensionista                                                                                  \n";
    $stSql .= "         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                   \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                         \n";
    $stSql .= "         UNION                                                                                                      \n";
    $stSql .= "        SELECT servidor_contrato_servidor.cod_contrato                                                              \n";
    $stSql .= "             , servidor.numcgm                                                                                      \n";
    $stSql .= "          FROM pessoal.servidor_contrato_servidor                                                                   \n";
    $stSql .= "             , pessoal.servidor                                                                                     \n";
    $stSql .= "         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor) as servidor                         \n";
    $stSql .= "     , sw_cgm_pessoa_fisica                                                                                         \n";
    $stSql .= "     , sw_cgm                                                                                                       \n";
    $stSql .= "     , folhapagamento.evento                                                                                        \n";
    $stSql .= " WHERE log.cod_evento       = registro_evento.cod_evento                                                            \n";
    $stSql .= "   AND log.cod_registro     = registro_evento.cod_registro                                                          \n";
    $stSql .= "   AND log.timestamp        = registro_evento.timestamp                                                             \n";
    $stSql .= "   AND registro_evento.cod_registro      = registro_evento_periodo.cod_registro                                     \n";
    $stSql .= "   AND registro_evento_periodo.cod_contrato             = contrato_servidor_periodo.cod_contrato                    \n";
    $stSql .= "   AND registro_evento_periodo.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao        \n";
    $stSql .= "   AND contrato_servidor_periodo.cod_contrato           = contrato.cod_contrato                                     \n";
    $stSql .= "   AND servidor.cod_contrato                            = contrato.cod_contrato                                     \n";
    $stSql .= "   AND servidor.numcgm                   = sw_cgm_pessoa_fisica.numcgm                                              \n";
    $stSql .= "   AND sw_cgm_pessoa_fisica.numcgm       = sw_cgm.numcgm                                                            \n";
    $stSql .= "   AND log.cod_evento                    = evento.cod_evento                                                        \n";

    return $stSql;
}

function recuperaLogErroCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaLogErroCalculo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLogErroCalculo()
{
    $stSql  = "SELECT log_erro_calculo.*                       \n";
    $stSql .= "     , (SELECT codigo FROM folhapagamento.evento WHERE cod_evento = log_erro_calculo.cod_evento) as codigo          \n";
    $stSql .= "  FROM folhapagamento.log_erro_calculo          \n";
    $stSql .= "     , folhapagamento.registro_evento           \n";
    $stSql .= "     , folhapagamento.registro_evento_periodo   \n";
    $stSql .= " WHERE log_erro_calculo.cod_evento       = registro_evento.cod_evento       \n";
    $stSql .= "   AND log_erro_calculo.cod_registro     = registro_evento.cod_registro     \n";
    $stSql .= "   AND log_erro_calculo.timestamp        = registro_evento.timestamp        \n";
    $stSql .= "   AND registro_evento.cod_registro      = registro_evento_periodo.cod_registro \n";

    return $stSql;

}

function recuperaErrosDoContrato(&$rsRecordSet, $stFiltro = "", $stOrdem = "",$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY ultimo_registro_evento.cod_registro ";
    $stSql = $this->montaRecuperaErrosDoContrato().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaErrosDoContrato()
{
    $stSql  = "SELECT ultimo_registro_evento.*                                                                                          \n";
    $stSql .= "     , registro_evento_periodo.cod_contrato                                                                              \n";
    $stSql .= "     , registro_evento_periodo.cod_periodo_movimentacao                                                                  \n";
    $stSql .= "     , log_erro_calculo.*                                                                                                \n";
    $stSql .= "     , sw_cgm.numcgm                                                                                                     \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                                    \n";
    $stSql .= "     , evento.codigo                                                                                                     \n";
    $stSql .= "     , contrato.registro                                                                                                 \n";
    $stSql .= "  FROM folhapagamento.ultimo_registro_evento                                                   \n";
    $stSql .= "     , folhapagamento.registro_evento_periodo                                                  \n";
    $stSql .= "     , folhapagamento.log_erro_calculo                                                         \n";
    $stSql .= "     , (SELECT servidor_contrato_servidor.cod_contrato                                                                   \n";
    $stSql .= "             , servidor.numcgm                                                                                           \n";
    $stSql .= "          FROM pessoal.servidor_contrato_servidor                                              \n";
    $stSql .= "             , pessoal.servidor                                                                \n";
    $stSql .= "         WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                           \n";
    $stSql .= "         UNION                                                                                                           \n";
    $stSql .= "        SELECT contrato_pensionista.cod_contrato                                                                         \n";
    $stSql .= "             , pensionista.numcgm                                                                                        \n";
    $stSql .= "          FROM pessoal.contrato_pensionista                                                    \n";
    $stSql .= "             , pessoal.pensionista                                                             \n";
    $stSql .= "         WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                        \n";
    $stSql .= "           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor_pensionista     \n";
    $stSql .= "     , sw_cgm                                                                                                            \n";
    $stSql .= "     , folhapagamento.evento                                                                   \n";
    $stSql .= "     , pessoal.contrato                                                                        \n";
    $stSql .= " WHERE ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro                                        \n";
    $stSql .= "   AND ultimo_registro_evento.cod_registro = log_erro_calculo.cod_registro                                               \n";
    $stSql .= "   AND ultimo_registro_evento.cod_evento = log_erro_calculo.cod_evento                                                   \n";
    $stSql .= "   AND ultimo_registro_evento.timestamp = log_erro_calculo.timestamp                                                     \n";
    $stSql .= "   AND registro_evento_periodo.cod_contrato = servidor_pensionista.cod_contrato                                          \n";
    $stSql .= "   AND servidor_pensionista.numcgm = sw_cgm.numcgm                                                                       \n";
    $stSql .= "   AND log_erro_calculo.cod_evento = evento.cod_evento                                                                   \n";
    $stSql .= "   AND servidor_pensionista.cod_contrato = contrato.cod_contrato                                                         \n";

    return $stSql;
}

}
