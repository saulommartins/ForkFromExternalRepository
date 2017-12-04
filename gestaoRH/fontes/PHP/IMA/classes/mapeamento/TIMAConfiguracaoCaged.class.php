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
    * Classe de mapeamento da tabela ima.configuracao_caged
    * Data de Criação: 18/04/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-tabelas

    $Id: TIMAConfiguracaoCaged.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.configuracao_caged
  * Data de Criação: 18/04/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMAConfiguracaoCaged extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAConfiguracaoCaged()
{
    parent::Persistente();
    $this->setTabela("ima.configuracao_caged");

    $this->setCampoCod('cod_configuracao');
    $this->setComplementoChave('');

    $this->AddCampo('cod_configuracao','sequence',true  ,''   ,true,false);
    $this->AddCampo('cod_cnae'        ,'integer' ,true  ,''   ,false,'TCEMCnaeFiscal');
    $this->AddCampo('tipo_declaracao' ,'char'    ,true  ,'1'  ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT configuracao_caged.*                                  \n";
    $stSql .= "     , cnae_fiscal.*                                         \n";
    $stSql .= "  FROM ima.configuracao_caged      \n";
    $stSql .= "     , economico.cnae_fiscal                                 \n";
    $stSql .= " WHERE configuracao_caged.cod_cnae = cnae_fiscal.cod_cnae    \n";

    return $stSql;
}

function recuperaCaged(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaCaged",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaCaged()
{
    $stSql = "select * FROM caged('".Sessao::getEntidade()."'
                                 ,".$this->getDado("sequencia")."
                                 ,'".$this->getDado("competencia")."'
                                 ,'".$this->getDado("tipo_filtro")."'
                                 ,'".$this->getDado("codigos")."'
                                 ,".$this->getDado("cod_atributo")."
                                 ,".$this->getDado("bo_array").");";

    return $stSql;
}

}
?>
