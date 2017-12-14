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
    * Classe de mapeamento da tabela pessoal.contrato_pensionista
    * Data de Criação: 15/08/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.contrato_pensionista
  * Data de Criação: 15/08/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoPensionista extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoPensionista()
{
    parent::Persistente();
    $this->setTabela("pessoal.contrato_pensionista");

    $this->setCampoCod('cod_contrato');
    $this->setComplementoChave('');

    $this->AddCampo('cod_contrato','integer',true,'',true,"TPessoalContrato");
    $this->AddCampo('cod_pensionista','integer',true,'',false,"TPessoalPensionista");
    $this->AddCampo('cod_contrato_cedente','integer',true,'',false,"TPessoalPensionista");
    $this->AddCampo('cod_dependencia' ,'integer',true,'',false,"TPessoalTipoDependencia");
    $this->AddCampo('num_beneficio','varchar',false,'15',false,false);
    $this->AddCampo('percentual_pagamento','numeric',false,'5,2',false,false);
    $this->AddCampo('dt_inicio_beneficio','date',true,'',false,false);
    $this->AddCampo('dt_encerramento','date',true,'',false,false);
    $this->AddCampo('motivo_encerramento','varchar',true,'200',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT contrato_pensionista.*                                               \n";
    $stSql .= "     , contrato.registro                                                    \n";
    $stSql .= "  FROM pessoal.contrato_pensionista                                         \n";
    $stSql .= "     , pessoal.pensionista                                                  \n";
    $stSql .= "     , pessoal.contrato                                                     \n";
    $stSql .= " WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista   \n";
    $stSql .= "   AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente \n";
    $stSql .= "   AND contrato_pensionista.cod_contrato = contrato.cod_contrato            \n";

    return $stSql;
}

function recuperaPensionistas(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY pensionista.numcgm ";
    $stSql = $this->montaRecuperaPensionistas().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPensionistas()
{
    $stSql  = "SELECT contrato_pensionista.*                                               \n";
    $stSql .= "     , to_char(dt_inicio_beneficio,'dd/mm/yyyy') as dt_inicio_beneficio     \n";
    $stSql .= "     , to_char(dt_encerramento,'dd/mm/yyyy') as dt_encerramento             \n";
    $stSql .= "     , pensionista.cod_grau                                                 \n";
    $stSql .= "     , pensionista.cod_profissao                                            \n";
    $stSql .= "     , sw_cgm.numcgm as numcgm_pensionista                                  \n";
    $stSql .= "     , sw_cgm.nom_cgm as nom_cgm_pensionista                                \n";
    $stSql .= "     , registro_pensionista.registro as registro_pensionista                \n";
    $stSql .= "     , registro_servidor.registro as registro_servidor                      \n";
    $stSql .= "     , sw_cgm_servidor.numcgm as numcgm_servidor                            \n";
    $stSql .= "     , sw_cgm_servidor.nom_cgm as nom_cgm_servidor                          \n";
    $stSql .= "  FROM pessoal.contrato_pensionista                                         \n";
    $stSql .= "LEFT JOIN pessoal.contrato as registro_pensionista                          \n";
    $stSql .= "       ON registro_pensionista.cod_contrato = contrato_pensionista.cod_contrato         \n";
    $stSql .= "     , pessoal.pensionista                                                  \n";
    $stSql .= "LEFT JOIN pessoal.contrato as registro_servidor                             \n";
    $stSql .= "       ON registro_servidor.cod_contrato = pensionista.cod_contrato_cedente \n";
    $stSql .= "LEFT JOIN sw_cgm                                                            \n";
    $stSql .= "       ON sw_cgm.numcgm = pensionista.numcgm                                \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                   \n";
    $stSql .= "     , pessoal.servidor                                                     \n";
    $stSql .= "LEFT JOIN sw_cgm as sw_cgm_servidor                                         \n";
    $stSql .= "       ON sw_cgm_servidor.numcgm = servidor.numcgm                          \n";
    $stSql .= " WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista   \n";
    $stSql .= "   AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente \n";
    $stSql .= "   AND pensionista.cod_contrato_cedente = servidor_contrato_servidor.cod_contrato \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor       \n";

    return $stSql;
}

}
