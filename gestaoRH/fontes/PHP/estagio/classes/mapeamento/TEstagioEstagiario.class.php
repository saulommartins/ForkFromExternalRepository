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
    * Classe de mapeamento da tabela estagio.estagiario
    * Data de Criação: 04/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  estagio.estagiario
  * Data de Criação: 04/10/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEstagioEstagiario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEstagioEstagiario()
{
    parent::Persistente();
    $this->setTabela("estagio.estagiario");

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm');

    $this->AddCampo('numcgm' ,'integer',true  ,''     ,true,'TCGMPessoaFisica');
    $this->AddCampo('nom_pai','varchar',true  ,'100'  ,false,false);
    $this->AddCampo('nom_mae','varchar',true  ,'100'  ,false,false);

}

function recuperaEstagiarioRemessaBanPara(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    if (trim($stOrdem)=="") {$stOrdem="ORDER BY nom_cgm";}
    $obErro = $this->executaRecupera("montaRecuperaEstagiarioRemessaBanPara",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaEstagiarioRemessaBanPara()
{
    $stSql .= "SELECT * FROM (\n";
    $stSql .= "          SELECT UPPER(sw_cgm.nom_cgm) as nom_cgm\n";
    $stSql .= "        , sw_cgm.numcgm\n";
    $stSql .= "        , UPPER(sw_cgm.bairro) as bairro\n";
    $stSql .= "        , sw_cgm.cep\n";
    $stSql .= "        , sw_cgm.cod_municipio\n";
    $stSql .= "        , sw_cgm.cod_pais\n";
    $stSql .= "        , sw_cgm.cod_uf\n";
    $stSql .= "        , UPPER(sw_cgm.complemento) as complemento\n";
    $stSql .= "        , sw_cgm.dt_cadastro\n";
    $stSql .= "        , sw_cgm.fone_residencial\n";
    $stSql .= "        , UPPER(sw_cgm.logradouro) as logradouro\n";
    $stSql .= "        , UPPER(sw_cgm.numero) as numero\n";
    $stSql .= "        , sw_cgm_pessoa_fisica.cod_escolaridade\n";
    $stSql .= "        , sw_cgm_pessoa_fisica.cod_uf_orgao_emissor\n";
    $stSql .= "        , sw_cgm_pessoa_fisica.cpf\n";
    $stSql .= "        , sw_cgm_pessoa_fisica.dt_nascimento\n";
    $stSql .= "        , UPPER(sw_cgm_pessoa_fisica.orgao_emissor) as orgao_emissor\n";
    $stSql .= "        , sw_cgm_pessoa_fisica.rg\n";
    $stSql .= "        , UPPER(sw_cgm_pessoa_fisica.sexo) as sexo\n";
    $stSql .= "        , ( SELECT UPPER(nom_municipio) as nom_municipio FROM sw_municipio WHERE cod_municipio = sw_cgm.cod_municipio and cod_uf = sw_cgm.cod_uf ) as cidade\n";
    $stSql .= "        , ( SELECT sigla_uf FROM sw_uf WHERE cod_uf = sw_cgm.cod_uf and cod_pais = sw_cgm.cod_pais ) as uf\n";
    $stSql .= "        , ( SELECT sigla_uf FROM sw_uf WHERE cod_uf = sw_cgm_pessoa_fisica.cod_uf_orgao_emissor and cod_pais = sw_cgm.cod_pais ) as uf_orgao_emissor\n";
    $stSql .= "        , estagiario_estagio_conta.cod_banco\n";
    $stSql .= "        , estagiario_estagio_conta.cod_agencia\n";
    $stSql .= "        , estagiario_estagio_conta.num_conta\n";
    $stSql .= "        , (SELECT num_banco FROM monetario.banco where banco.cod_banco = estagiario_estagio_conta.cod_banco) as num_banco\n";
    $stSql .= "        , (SELECT num_agencia FROM monetario.agencia where agencia.cod_banco = estagiario_estagio_conta.cod_banco and agencia.cod_agencia = estagiario_estagio_conta.cod_agencia) as num_agencia\n";
    $stSql .= "        , estagiario_estagio.cgm_estagiario\n";
    $stSql .= "        , estagiario_estagio.cgm_instituicao_ensino\n";
    $stSql .= "        , estagiario_estagio.cod_curso\n";
    $stSql .= "        , estagiario_estagio.cod_estagio\n";
    $stSql .= "        , estagiario_estagio.numero_estagio\n";
    $stSql .= "        , estagiario_estagio.dt_inicio\n";
    $stSql .= "        , UPPER(estagiario.nom_pai) as nom_pai\n";
    $stSql .= "        , UPPER(estagiario.nom_mae) as nom_mae\n";
    $stSql .= "        , estagiario_estagio_local.cod_local\n";
    $stSql .= "        , estagiario_estagio.cod_orgao\n";
    $stSql .= "        , estagiario_estagio.dt_final\n";
    $stSql .= "     FROM estagio.estagiario_estagio\n";
    $stSql .= "LEFT JOIN estagio.estagiario_estagio_conta\n";
    $stSql .= "       ON estagiario_estagio.cod_estagio = estagiario_estagio_conta.cod_estagio\n";
    $stSql .= "      AND estagiario_estagio.cgm_estagiario = estagiario_estagio_conta.numcgm\n";
    $stSql .= "      AND estagiario_estagio.cod_curso = estagiario_estagio_conta.cod_curso\n";
    $stSql .= "      AND estagiario_estagio.cgm_instituicao_ensino = estagiario_estagio_conta.cgm_instituicao_ensino\n";
    $stSql .= "LEFT JOIN estagio.estagiario_estagio_local\n";
    $stSql .= "       ON estagiario_estagio.cod_estagio = estagiario_estagio_local.cod_estagio\n";
    $stSql .= "      AND estagiario_estagio.cgm_estagiario = estagiario_estagio_local.numcgm\n";
    $stSql .= "      AND estagiario_estagio.cod_curso = estagiario_estagio_local.cod_curso\n";
    $stSql .= "      AND estagiario_estagio.cgm_instituicao_ensino = estagiario_estagio_local.cgm_instituicao_ensino\n";
    $stSql .= "        , estagio.curso_instituicao_ensino\n";
    $stSql .= "        , estagio.estagiario\n";
    $stSql .= "        , sw_cgm\n";
    $stSql .= "        , sw_cgm_pessoa_fisica\n";
    $stSql .= "    WHERE estagiario_estagio.cgm_estagiario = sw_cgm.numcgm\n";
    $stSql .= "      AND sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm\n";
    $stSql .= "      AND estagiario_estagio.cgm_estagiario = estagiario.numcgm\n";
    $stSql .= "      AND estagiario_estagio.cod_curso = curso_instituicao_ensino.cod_curso\n";
    $stSql .= "      AND estagiario_estagio.cgm_instituicao_ensino = curso_instituicao_ensino.numcgm\n";
    $stSql .= ") AS estagio \n";

    return $stSql;
}

}
?>
