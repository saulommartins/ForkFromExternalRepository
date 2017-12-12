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
* Classe de mapeamento para administracao.usuario
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 15598 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 11:21:57 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.03.93
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
    * Efetua conexão com tabela Usuario
    * @author Diego Barbosa Victoria
*/

class TUsuario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TUsuario()
{
    parent::Persistente();
    $this->setTabela('administracao.usuario');
    $this->setCampoCod('numcgm');

    $this->AddCampo('numcgm'            ,'integer',true ,''  ,true,'TCGM');
    $this->AddCampo('cod_orgao'         ,'integer',true ,''  ,false,true);
    $this->AddCampo('cod_unidade'       ,'integer',true ,''  ,false,true);
    $this->AddCampo('cod_departamento'  ,'integer',true ,''  ,false,true);
    $this->AddCampo('cod_setor'         ,'integer',true ,''  ,false,true);
    $this->AddCampo('ano_exercicio'     ,'varchar',true ,'4' ,false,false);
    $this->AddCampo('dt_cadastro'       ,'date'   ,true ,''  ,false,false);
    $this->AddCampo('username'          ,'varchar',false ,'15',false,false);
    $this->AddCampo('password'          ,'varchar',true ,'34',false,false, "");
    $this->AddCampo('status'            ,'varchar',false,'1',false,false);
}

/**
    * Monta consulta para recuperar usuarios
    * @access Private
    * @return String $stSql
*/
function montaRecuperaRelacionamento()
{
    $stSql  = "
     select usuario.numcgm
          , usuario.cod_orgao
          , ( select orgao_descricao.descricao
                from organograma.orgao_descricao
               where orgao_descricao.cod_orgao = usuario.cod_orgao
            order by timestamp desc
               limit 1
          ) as orgao
          , TO_CHAR( usuario.dt_cadastro, 'dd/mm/yyyy') as dt_cadastro
          , usuario.username
          , usuario.password
          , usuario.status
          , sw_cgm.nom_cgm
          , ( select orgao_descricao.descricao
                from organograma.orgao_descricao
               where orgao_descricao.cod_orgao = usuario.cod_orgao
            order by timestamp desc
               limit 1
          ) as nom_setor  -- para compatibilidade
       from administracao.usuario
            inner join sw_cgm
                    on sw_cgm.numcgm = usuario.numcgm
    ";

    return $stSql;
}
/**
    * Recupera os Usuarios
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaUsuario(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado( "stFiltro", $stCondicao );

    if (trim($stOrdem)) {
        $stOrdem = strtoupper( $stOrdem );
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaRelacionamento().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function alterarStatus($boTransacao)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql = $this->montaAlterarStatus();

    return  $obConexao->executaDML( $stSql , $boTransacao );
}

function montaAlterarStatus()
{
    if ( strpos( $this->getDado('username'), "sw." ) === false ) {
        $stUsuario = "sw.".$this->getDado('username');
    } else {
        $stUsuario = $this->getDado('username');
    }
    if ( $this->getDado('status') == "A" ) {
        $stSQL = " ALTER USER \"".$stUsuario."\" VALID UNTIL 'infinity'";
    } elseif ( $this->getDado('status') == "I" ) {
        $stSQL = " ALTER USER \"".$stUsuario."\" VALID UNTIL '1900-01-01'";
    }

    return $stSQL;
}

}

class TAdministracaoUsuario extends TUsuario
{
    public function TAdministracaoUsuario()
    {
        parent::TUsuario();
    }
}
