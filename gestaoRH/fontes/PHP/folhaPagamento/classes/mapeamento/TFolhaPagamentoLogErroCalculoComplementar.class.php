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
    * Classe de mapeamento da tabela folhapagamento.log_erro_calculo_complementar
    * Data de Criação: 25/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-10 13:08:17 -0300 (Qua, 10 Out 2007) $

    * Casos de uso: uc-04.05.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.log_erro_calculo_complementar
  * Data de Criação: 25/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoLogErroCalculoComplementar extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoLogErroCalculoComplementar()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.log_erro_calculo_complementar");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_evento,cod_registro,cod_configuracao');

    $this->AddCampo('cod_evento','integer',true,'',true         ,"TFolhaPagamentoUltimoRegistroEventoComplementar");
    $this->AddCampo('cod_registro','integer',true,'',true       ,"TFolhaPagamentoUltimoRegistroEventoComplementar");
    $this->AddCampo('timestamp','timestamp',false,'',true       ,"TFolhaPagamentoUltimoRegistroEventoComplementar");
    $this->AddCampo('cod_configuracao','integer',true,'',true   ,"TFolhaPagamentoUltimoRegistroEventoComplementar");
    $this->AddCampo('erro','varchar',true,'varchar',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT * FROM (                                                                      \n";
    $stSql .= "SELECT evento.codigo                                                                 \n";
    $stSql .= "     , log_erro_calculo_complementar.erro                                            \n";
    $stSql .= "     , contrato.registro                                                             \n";
    $stSql .= "     , contrato.cod_contrato                                                         \n";
    $stSql .= "     , servidor.numcgm                                                               \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                \n";
    $stSql .= "  FROM folhapagamento.log_erro_calculo_complementar                                  \n";
    $stSql .= "     , folhapagamento.registro_evento_complementar                                   \n";
    $stSql .= "     , folhapagamento.evento                                                         \n";
    $stSql .= "     , folhapagamento.contrato_servidor_complementar                                 \n";
    $stSql .= "     , pessoal.contrato_servidor                                                     \n";
    $stSql .= "     , pessoal.contrato                                                              \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                            \n";
    $stSql .= "     , pessoal.servidor                                                              \n";
    $stSql .= "     , sw_cgm_pessoa_fisica                                                          \n";
    $stSql .= "     , sw_cgm                                                                        \n";
    $stSql .= " WHERE log_erro_calculo_complementar.cod_registro = registro_evento_complementar.cod_registro\n";
    $stSql .= "   AND log_erro_calculo_complementar.cod_evento   = registro_evento_complementar.cod_evento  \n";
    $stSql .= "   AND log_erro_calculo_complementar.timestamp    = registro_evento_complementar.timestamp   \n";
    $stSql .= "   AND log_erro_calculo_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao \n";
    $stSql .= "   AND registro_evento_complementar.cod_periodo_movimentacao = contrato_servidor_complementar.cod_periodo_movimentacao \n";
    $stSql .= "   AND registro_evento_complementar.cod_complementar         = contrato_servidor_complementar.cod_complementar         \n";
    $stSql .= "   AND registro_evento_complementar.cod_contrato             = contrato_servidor_complementar.cod_contrato             \n";
    $stSql .= "   AND registro_evento_complementar.cod_evento               = evento.cod_evento                                       \n";
    $stSql .= "   AND contrato_servidor_complementar.cod_contrato           = contrato_servidor.cod_contrato                          \n";
    $stSql .= "   AND contrato_servidor.cod_contrato                        = contrato.cod_contrato                                   \n";
    $stSql .= "   AND contrato_servidor.cod_contrato                        = servidor_contrato_servidor.cod_contrato                 \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor               = servidor.cod_servidor                                   \n";
    $stSql .= "   AND servidor.numcgm                                       = sw_cgm_pessoa_fisica.numcgm                             \n";
    $stSql .= "   AND sw_cgm_pessoa_fisica.numcgm                           = sw_cgm.numcgm                                           \n";
    $stSql .= "GROUP BY evento.codigo                                                               \n";
    $stSql .= "       , log_erro_calculo_complementar.erro                                          \n";
    $stSql .= "       , contrato.registro                                                           \n";
    $stSql .= "       , contrato.cod_contrato                                                       \n";
    $stSql .= "       , servidor.numcgm                                                             \n";
    $stSql .= "       , sw_cgm.nom_cgm) as log_erro_calculo_complementar                                          \n";

    return $stSql;
}

function recuperaLogErroCalculadoComplementar(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaLogErroCalculadoComplementar().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLogErroCalculadoComplementar()
{
    $stSql .= "SELECT log_erro_calculo_complementar.*                       \n";
    $stSql .= "     , codigo                                               \n";
    $stSql .= "  FROM folhapagamento.log_erro_calculo_complementar          \n";
    $stSql .= "     , folhapagamento.registro_evento_complementar           \n";
    $stSql .= "     , folhapagamento.evento                                 \n";
    $stSql .= " WHERE log_erro_calculo_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao \n";
    $stSql .= "   AND log_erro_calculo_complementar.cod_evento       = registro_evento_complementar.cod_evento       \n";
    $stSql .= "   AND log_erro_calculo_complementar.cod_registro     = registro_evento_complementar.cod_registro     \n";
    $stSql .= "   AND log_erro_calculo_complementar.timestamp        = registro_evento_complementar.timestamp        \n";
    $stSql .= "   AND log_erro_calculo_complementar.cod_evento       = evento.cod_evento       \n";

    return $stSql;

}

function recuperaContratosComErro(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaContratosComErro", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaContratosComErro()
{
    $stSql .= "    SELECT contrato.*\n";
    $stSql .= "         , servidor.numcgm\n";
    $stSql .= "         , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm\n";
    $stSql .= "         , log_erro_calculo_complementar.erro\n";
    $stSql .= "         , (SELECT codigo FROM folhapagamento.evento WHERE cod_evento = log_erro_calculo_complementar.cod_evento) as codigo\n";
    $stSql .= "      FROM pessoal.contrato\n";
    $stSql .= "INNER JOIN (    SELECT servidor_contrato_servidor.cod_contrato\n";
    $stSql .= "                     , servidor.numcgm\n";
    $stSql .= "                  FROM pessoal.servidor_contrato_servidor\n";
    $stSql .= "            INNER JOIN pessoal.servidor\n";
    $stSql .= "                    ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor\n";
    $stSql .= "                 UNION \n";
    $stSql .= "                SELECT contrato_pensionista.cod_contrato\n";
    $stSql .= "                     , pensionista.numcgm\n";
    $stSql .= "                 FROM pessoal.contrato_pensionista\n";
    $stSql .= "           INNER JOIN pessoal.pensionista\n";
    $stSql .= "                   ON pensionista.cod_pensionista = contrato_pensionista.cod_pensionista\n";
    $stSql .= "                  AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente) as servidor\n";
    $stSql .= "        ON servidor.cod_contrato = contrato.cod_contrato\n";
    $stSql .= "INNER JOIN folhapagamento.registro_evento_complementar\n";
    $stSql .= "        ON registro_evento_complementar.cod_contrato = contrato.cod_contrato\n";
    $stSql .= "INNER JOIN folhapagamento.log_erro_calculo_complementar\n";
    $stSql .= "        ON log_erro_calculo_complementar.cod_registro = registro_evento_complementar.cod_registro\n";
    $stSql .= "       AND log_erro_calculo_complementar.cod_evento = registro_evento_complementar.cod_evento\n";
    $stSql .= "       AND log_erro_calculo_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao\n";
    $stSql .= "       AND log_erro_calculo_complementar.timestamp = registro_evento_complementar.timestamp\n";

    return $stSql;

}

}
