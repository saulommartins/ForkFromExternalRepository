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
    * Classe de mapeamento da tabela ima.ocorrencia_detalhe_910
    * Data de Criação: 02/06/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.21

    $Id: TIMAOcorrenciaDetalhe910.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.ocorrencia_detalhe_910
  * Data de Criação: 02/06/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMAOcorrenciaDetalhe910 extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAOcorrenciaDetalhe910()
{
    parent::Persistente();
    $this->setTabela("ima.ocorrencia_detalhe_910");

    $this->setCampoCod('num_ocorrencia');
    $this->setComplementoChave('');

    $this->AddCampo('num_ocorrencia','sequence',true  ,''     ,true ,false);
    $this->AddCampo('posicao'       ,'integer' ,true  ,''     ,false,false);
    $this->AddCampo('descricao'     ,'varchar' ,true  ,'180'  ,false,false);

}

function recuperaDadosServidor(&$rsRecordSet,$stFiltro="",$stOrdem="")
{
    $obErro = $this->executaRecupera("montaRecuperaDadosServidor",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaDadosServidor()
{
    $stSql  = "    SELECT servidor.numcgm																									\n";
    $stSql .= "         , servidor_contrato_servidor.cod_contrato																			\n";
    $stSql .= "         , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm                                			\n";
    $stSql .= "         , sw_cgm_pessoa_fisica.servidor_pis_pasep as pis_pasep     															\n";
    $stSql .= "         , to_char(contrato_servidor_nomeacao_posse.dt_admissao,'dd/mm/yyyy') as dt_admissao                                 \n";
    $stSql .= "         , contrato.registro as matricula								                                 					\n";
    $stSql .= "      FROM pessoal.servidor																									\n";
    $stSql .= "INNER JOIN sw_cgm_pessoa_fisica																								\n";
    $stSql .= "        ON servidor.numcgm = sw_cgm_pessoa_fisica.numcgm																		\n";
    $stSql .= "INNER JOIN ( SELECT cod_servidor																								\n";
    $stSql .= "                  , max(cod_contrato) as cod_contrato																		\n";
    $stSql .= "               FROM pessoal.servidor_contrato_servidor																		\n";
    $stSql .= "              WHERE recuperarSituacaoDoContrato(servidor_contrato_servidor.cod_contrato,0,'".Sessao::getEntidade()."') = 'A'	\n";
    $stSql .= "           GROUP BY cod_servidor ) as servidor_contrato_servidor																\n";
    $stSql .= "        ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor    												\n";
    $stSql .= "INNER JOIN ultimo_contrato_servidor_nomeacao_posse('".Sessao::getEntidade()."', 0) as contrato_servidor_nomeacao_posse       \n";
    $stSql .= "        ON servidor_contrato_servidor.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato                           \n";
    $stSql .= "INNER JOIN pessoal.contrato																									\n";
    $stSql .= "        ON servidor_contrato_servidor.cod_contrato = contrato.cod_contrato													\n";

    return $stSql;
}

}
?>
