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
* Classe de mapeamento para administracao.configuracao
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Id: TAdministracaoConfiguracao.class.php 59612 2014-09-02 12:00:51Z gelson $

$Revision: 28796 $
$Name$
$Author: luiz $
$Date: 2008-03-26 16:59:44 -0300 (Qua, 26 Mar 2008) $

Casos de uso: uc-01.03.97
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TAdministracaoConfiguracao extends Persistente
{
function TAdministracaoConfiguracao()
{
    parent::Persistente();
    $this->setTabela('administracao.configuracao');
    $this->setComplementoChave('cod_modulo,parametro,exercicio');

    $this->AddCampo('cod_modulo', 'integer', true, '',  true,  true);
    $this->AddCampo('parametro',  'varchar', true, 40,  true, false);
    $this->AddCampo('valor',      'text',    true, '', false, false);
    $this->AddCampo('exercicio',  'char',    false, 4,  true, false);
}

function pegaConfiguracao(&$stValor, $stParametro, $boTransacao = '')
{
    if( $stParametro!='' )
        $this->setDado( "parametro", $stParametro );

    $obErro = $this->recuperaPorChave( $rsConfiguracao , $boTransacao );
    if (!$obErro->ocorreu()) {
        $stValor = $rsConfiguracao->getCampo( "valor" );
        $this->setDado('valor', $stValor);
    }

    return $obErro;
}

/**
    * Verifica se o parametro existe na tabela administracao.configuracao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function verificaParametroConfiguracao(&$rsRecordSet, $stParametro, $boTransacao = '')
{
    if( $stParametro!='' )
        $this->setDado( "parametro", $stParametro );

    $obErro = $this->recuperaPorChave( $rsRecordSet , $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMunicipio(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaMunicipio();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMunicipio()
{
    $stSQL  = "SELECT  M.cod_municipio \n";
    $stSQL .= "       ,M.nom_municipio \n";
    $stSQL .= "       ,UF.cod_uf  \n";
    $stSQL .= "       ,UF.nom_uf  \n";
    $stSQL .= "       ,UF.sigla_uf \n";
    $stSQL .= "FROM sw_municipio  AS  M \n";
    $stSQL .= "     ,sw_uf        AS UF \n";
    $stSQL .= "WHERE M.cod_municipio::text IN ( SELECT valor \n";
    $stSQL .= "                         FROM administracao.configuracao \n";
    $stSQL .= "                         WHERE parametro = 'cod_municipio' \n";
    if($this->getDado('exercicio'))
        $stSQL .= "                       AND exercicio = '".$this->getDado('exercicio')."' \n";
        $stSQL .= "                                                             ) \n";
        $stSQL .= "AND M.cod_uf::text IN ( SELECT valor \n";
        $stSQL .= "                FROM administracao.configuracao \n";
        $stSQL .= "                WHERE parametro ='cod_uf' \n";
    if($this->getDado('exercicio'))
        $stSQL .= "              AND exercicio = '".$this->getDado('exercicio')."' \n";
        $stSQL .= "                                             ) \n";
        $stSQL .= "AND M.cod_uf = UF.cod_uf \n";

    return $stSQL;
}

function recuperaBimestre(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
       $stSql= " SELECT * FROM publico.bimestre ('". $this->getDado('exercicio'). "'," .$this->getDado('cmbBimestre').")";

       return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

}
