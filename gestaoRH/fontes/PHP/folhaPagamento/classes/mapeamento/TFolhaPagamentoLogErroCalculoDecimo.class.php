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
    * Classe de mapeamento da tabela folhapagamento.log_erro_calculo_decimo
    * Data de Criação: 06/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.log_erro_calculo_decimo
  * Data de Criação: 06/09/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoLogErroCalculoDecimo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoLogErroCalculoDecimo()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.log_erro_calculo_decimo");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_evento,cod_registro,timestamp,desdobramento');

    $this->AddCampo('cod_evento','integer',true,'',true     ,'TFolhaPagamentoUltimoRegistroEventoDecimo');
    $this->AddCampo('cod_registro','integer',true,'',true   ,'TFolhaPagamentoUltimoRegistroEventoDecimo');
    $this->AddCampo('timestamp','timestamp',false,'',true   ,'TFolhaPagamentoUltimoRegistroEventoDecimo');
    $this->AddCampo('desdobramento','char',true,'1',true    ,'TFolhaPagamentoUltimoRegistroEventoDecimo');
    $this->AddCampo('erro','varchar',true,'2000',false,false);

}

function recuperaErrosDoContrato(&$rsRecordSet, $stFiltro = "", $stOrdem = "",$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY ultimo_registro_evento_decimo.cod_registro ";
    $stSql = $this->montaRecuperaErrosDoContrato().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaErrosDoContrato()
{
    $stSql .= "SELECT ultimo_registro_evento_decimo.*                                                      \n";
    $stSql .= "     , registro_evento_decimo.cod_contrato                                                  \n";
    $stSql .= "     , log_erro_calculo_decimo.*                                                            \n";
    $stSql .= "     , sw_cgm.numcgm                                                                        \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                       \n";
    $stSql .= "     , evento.codigo                                                                        \n";
    $stSql .= "     , contrato.registro                                                                    \n";
    $stSql .= "  FROM folhapagamento.ultimo_registro_evento_decimo                                         \n";
    $stSql .= "     , folhapagamento.registro_evento_decimo                                                \n";
    $stSql .= "     , folhapagamento.log_erro_calculo_decimo                                               \n";
//    $stSql .= "     , pessoal.servidor_contrato_servidor                                                   \n";
//    $stSql .= "     , pessoal.servidor                                                                     \n";

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

    $stSql .= "     , sw_cgm                                                                               \n";
    $stSql .= "     , folhapagamento.evento                                                                \n";
    $stSql .= "     , pessoal.contrato                                                                     \n";
    $stSql .= " WHERE ultimo_registro_evento_decimo.cod_registro = registro_evento_decimo.cod_registro     \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.cod_evento = registro_evento_decimo.cod_evento         \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.timestamp = registro_evento_decimo.timestamp           \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.cod_registro = log_erro_calculo_decimo.cod_registro    \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.cod_evento = log_erro_calculo_decimo.cod_evento        \n";
    $stSql .= "   AND ultimo_registro_evento_decimo.timestamp = log_erro_calculo_decimo.timestamp          \n";
    $stSql .= "   AND registro_evento_decimo.cod_contrato = servidor.cod_contrato        \n";
//    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                      \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm.numcgm                                                      \n";
    $stSql .= "   AND log_erro_calculo_decimo.cod_evento = evento.cod_evento                               \n";
    $stSql .= "   AND servidor.cod_contrato = contrato.cod_contrato                      \n";

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
    $stSql .= "         , log_erro_calculo_decimo.erro\n";
    $stSql .= "         , (SELECT codigo FROM folhapagamento.evento WHERE cod_evento = log_erro_calculo_decimo.cod_evento) as codigo\n";
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
    $stSql .= "INNER JOIN folhapagamento.registro_evento_decimo\n";
    $stSql .= "        ON registro_evento_decimo.cod_contrato = contrato.cod_contrato\n";
    $stSql .= "INNER JOIN folhapagamento.log_erro_calculo_decimo\n";
    $stSql .= "        ON log_erro_calculo_decimo.cod_registro = registro_evento_decimo.cod_registro\n";
    $stSql .= "       AND log_erro_calculo_decimo.cod_evento = registro_evento_decimo.cod_evento\n";
    $stSql .= "       AND log_erro_calculo_decimo.desdobramento = registro_evento_decimo.desdobramento\n";
    $stSql .= "       AND log_erro_calculo_decimo.timestamp = registro_evento_decimo.timestamp\n";

    return $stSql;
}

}
