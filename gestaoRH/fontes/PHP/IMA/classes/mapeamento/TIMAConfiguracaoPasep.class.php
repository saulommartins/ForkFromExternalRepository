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
    * Classe de mapeamento da tabela ima.configuracao_pasep
    * Data de Criação: 29/05/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.22

    $Id: TIMAConfiguracaoPasep.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.configuracao_pasep
  * Data de Criação: 29/05/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMAConfiguracaoPasep extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAConfiguracaoPasep()
{
    parent::Persistente();
    $this->setTabela("ima.configuracao_pasep");

    $this->setCampoCod('cod_convenio');
    $this->setComplementoChave('');

    $this->AddCampo('cod_convenio'      ,'sequence',true  ,''    ,true,false);
    $this->AddCampo('num_convenio'      ,'integer' ,true  ,''    ,false,false);
    $this->AddCampo('cod_banco'         ,'integer' ,true  ,''    ,false,'TMONContaCorrente');
    $this->AddCampo('cod_agencia'       ,'integer' ,true  ,''    ,false,'TMONContaCorrente');
    $this->AddCampo('cod_conta_corrente','integer' ,true  ,''    ,false,'TMONContaCorrente');
    $this->AddCampo('cod_evento'        ,'integer' ,true  ,''    ,false,'TFolhaPagamentoEvento');
    $this->AddCampo('email'             ,'varchar' ,true  ,'50'  ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT configuracao_pasep.*                                                 			   \n";
    $stSql .= "     , conta_corrente.num_conta_corrente                                                \n";
    $stSql .= "     , agencia.num_agencia                                                              \n";
    $stSql .= "     , agencia.nom_agencia                                                              \n";
    $stSql .= "     , banco.num_banco                                                                  \n";
    $stSql .= "     , banco.nom_banco                                                                  \n";
    $stSql .= "     , evento.codigo                                                                    \n";
    $stSql .= "     , evento.descricao                                                                 \n";
    $stSql .= "  FROM ima.configuracao_pasep                     									   \n";
    $stSql .= "     , monetario.conta_corrente                                                         \n";
    $stSql .= "     , monetario.agencia                                                                \n";
    $stSql .= "     , monetario.banco                                                                  \n";
    $stSql .= "     , folhapagamento.evento                                  						   \n";
    $stSql .= " WHERE configuracao_pasep.cod_conta_corrente = conta_corrente.cod_conta_corrente        \n";
    $stSql .= "   AND configuracao_pasep.cod_agencia = conta_corrente.cod_agencia                      \n";
    $stSql .= "   AND configuracao_pasep.cod_banco   = conta_corrente.cod_banco                        \n";
    $stSql .= "   AND conta_corrente.cod_agencia = agencia.cod_agencia                                 \n";
    $stSql .= "   AND conta_corrente.cod_banco   = agencia.cod_banco                                   \n";
    $stSql .= "   AND agencia.cod_banco = banco.cod_banco                                              \n";
    $stSql .= "   AND configuracao_pasep.cod_evento   = evento.cod_evento                              \n";

    return $stSql;
}

function recuperaExportarArquivoFPS900(&$rsRecordSet,$stFiltro="",$stOrdem="")
{
    $obErro = $this->executaRecupera("montaRecuperaExportarArquivoFPS900",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaExportarArquivoFPS900()
{
    $stSql  = "    SELECT contrato.*                                                                                                \n";
    $stSql .= "         , TRIM(UPPER(TRANSLATE(REGEXP_REPLACE(sw_cgm.nom_cgm,'[\ ]+',' ', 'g'),'.-\\\\/*',''))) as nom_cgm                                                \n";
    $stSql .= "         , sw_cgm_pessoa_fisica.servidor_pis_pasep                                                                   \n";
    $stSql .= "         , TRANSLATE(sw_cgm_pessoa_fisica.servidor_pis_pasep,'.-','') as servidor_pis_pasep_formatado                \n";
    $stSql .= "         , TRIM(UPPER(TRANSLATE(REGEXP_REPLACE(sw_cgm.logradouro,'[\ ]+',' ', 'g'),'.-\\\\/*',''))) as logradouro                                          \n";
    $stSql .= "         , sw_cgm.numero                                                                                             \n";
    $stSql .= "         , TRIM(UPPER(TRANSLATE(REGEXP_REPLACE(sw_cgm.complemento,'[\ ]+',' ', 'g'),'.-\\\\/*',''))) as complemento                                        \n";
    $stSql .= "         , TRIM(UPPER(TRANSLATE(REGEXP_REPLACE(sw_cgm.bairro,'[\ ]+',' ', 'g'),'.-\\\\/*',''))) as bairro                                                  \n";
    $stSql .= "         , (SELECT TRIM(UPPER(TRANSLATE(REGEXP_REPLACE(nom_municipio,'[\ ]+',' ', 'g'),'.-\\\\/*',''))) FROM sw_municipio WHERE cod_municipio = sw_cgm.cod_municipio AND cod_uf = sw_cgm.cod_uf) as nom_municipio \n";
    $stSql .= "         , (SELECT UPPER(sigla_uf) FROM sw_uf WHERE cod_uf = sw_cgm.cod_uf) as sigla_uf                              \n";
    $stSql .= "         , TRANSLATE(sw_cgm.cep,'-','') as cep                                                                       \n";
    $stSql .= "      FROM pessoal.contrato                                                                 							\n";
    $stSql .= " LEFT JOIN ultimo_contrato_servidor_local('".Sessao::getEntidade()."',0) as contrato_servidor_local				    \n";
    $stSql .= " 	   ON contrato_servidor_local.cod_contrato = contrato.cod_contrato 												\n";
    $stSql .= "INNER JOIN ultimo_contrato_servidor_orgao('".Sessao::getEntidade()."',0) as contrato_servidor_orgao				    \n";
    $stSql .= " 	   ON contrato_servidor_orgao.cod_contrato = contrato.cod_contrato 												\n";

    if ($this->getDado("boAtributo") == "true") {
        $stSql .= "INNER JOIN ultimo_atributo_contrato_servidor_valor('".Sessao::getEntidade()."',0) as atributo_contrato_servidor_valor \n";
        $stSql .= "        ON contrato.cod_contrato = atributo_contrato_servidor_valor.cod_contrato										 \n";
    }

    $stSql .= "     , pessoal.servidor_contrato_servidor                                               							\n";
    $stSql .= "     , pessoal.servidor                                                                 							\n";
    $stSql .= "     , sw_cgm_pessoa_fisica                                                                                      \n";
    $stSql .= "     , sw_cgm                                                                                                    \n";
    $stSql .= " WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato                                           \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                           \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                             \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm.numcgm                                                                           \n";
    $stSql .= "   AND recuperarSituacaoDoContrato(contrato.cod_contrato,0,'".Sessao::getEntidade()."') NOT IN ('R','P')		     \n";

    return $stSql;
}

}
?>
