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
* Classe de mapeamento para administracao.atributo_dinamico
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28057 $
$Name$
$Author: domluc $
$Date: 2008-02-14 15:48:07 -0200 (Qui, 14 Fev 2008) $

Casos de uso: uc-01.03.96
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/**
  * Efetua conexão com a tabela  ADMINISTRACAO.ATRIBUTO_DINAMICO
  * Data de Criação: 09/08/2005

  * @author Analista: Cassiano de Vasconcellos Ferreira
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAdministracaoAtributoDinamico extends PersistenteAtributos
{
/**
    * Método Construtor
    * @access Private
*/
function TAdministracaoAtributoDinamico()
{
    parent::PersistenteAtributos();
    $this->setTabela('administracao.atributo_dinamico');

    $this->setCampoCod('cod_atributo');
    $this->setComplementoChave('cod_modulo,cod_cadastro');

    $this->AddCampo('cod_modulo','integer',true,'',true,true);
    $this->AddCampo('cod_cadastro','integer',true,'',true,true);
    $this->AddCampo('cod_atributo','integer',true,'',true,false);
    $this->AddCampo('cod_tipo','integer',true,'',false,false);
    $this->AddCampo('nao_nulo','bool',true,'',false,false);
    $this->AddCampo('nom_atributo','varchar',true,'80',false,false);
//    $this->AddCampo('valor_padrao','text',true,'',false,false);
    $this->AddCampo('ajuda','varchar',true,'80',false,false);
    $this->AddCampo('mascara','varchar',true,'40',false,false);
    $this->AddCampo('ativo','bool',true,'',false,false,'true');
    $this->AddCampo('interno','bool',false,'',false,false,'false');
    $this->AddCampo('indexavel','bool',false,'',false,false);

}
     /* hack tosco incrementar o cod_atributo sem filtro de modulo e cadastro */
     function proximoCod(&$inCod, $boTransacao = "")
     {
         $obErro      = new Erro;
         $obConexao   = new Conexao;
         $rsRecordSet = new RecordSet;
         $stComplemento = "";

         $stSql = "SELECT MAX(".$this->getCampoCod().") AS CODIGO FROM ".$this->getTabela().$stComplemento;
         $this->setDebug( $stSql );
         $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
         if ( !$obErro->ocorreu() ) {
             $inCod = $rsRecordSet->getCampo("codigo") + 1;
         }

         return $obErro;
     }

    public function montaDisponiveis(&$rsRecordSet, $boTransacao = "")
    {
        $stSql .= "  WHERE                                                      \n";
        $stSql .= "     AD.cod_atributo NOT IN (                                \n";
        $stSql .= "     SELECT cod_atributo FROM                                \n";
        $stSql .= "     sw_atributo_compras_almoxarifado                       \n";
        $stSql .= "     WHERE ativo = 't'                                       \n";
        $stSql .= "     AND cod_cadastro = ".$this->getDado('cod_cadastro')."   \n";
        $stSql .= "     ) AND AD.cod_modulo = 13                                \n";

        $obErro   = $this->recuperaTodos( $rsRecordSet,$stFiltro, "", $boTransacao);

        return $obErro;
    }

    public function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
         $obErro      = new Erro;
         $obConexao   = new Conexao;

         $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
         $this->setDebug( $stSql );
         $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

         return $obErro;
     }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = " SELECT                                       \n";
        $stSql .= "     ad.cod_modulo,                           \n";
        $stSql .= "     ad.cod_cadastro,                         \n";
        $stSql .= "     ad.cod_atributo,                         \n";
        $stSql .= "     ad.cod_tipo,                             \n";
        $stSql .= "     case when AD.nao_nulo = 't' then 'Sim'   \n";
        $stSql .= "         else 'Não' end as nao_nulo,          \n";
        $stSql .= "     ad.nom_atributo,                         \n";
        $stSql .= "     ad.valor_padrao,                         \n";
        $stSql .= "     ad.ajuda,                                \n";
        $stSql .= "     ad.mascara,                              \n";
        $stSql .= "     ad.ativo,                                \n";
        $stSql .= "     case when ad.ativo = 't' then 'Sim'      \n";
        $stSql .= "         else 'Não' end as stAtivo,           \n";
        $stSql .= "     ad.interno,                              \n";
        $stSql .= "     ad.indexavel,                            \n";
        $stSql .= "     ta.descricao,                            \n";
        $stSql .= "     ta.nom_tipo,                             \n";
        $stSql .= "     c.nom_cadastro                           \n";
        $stSql .= " FROM                                         \n";
        $stSql .= "     administracao.atributo_dinamico AS ad,\n";
        $stSql .= "     administracao.tipo_atributo     AS ta,\n";
        $stSql .= "     administracao.cadastro          AS c  \n";
        $stSql .= " WHERE                                        \n";
        $stSql .= "     ad.cod_tipo = ta.cod_tipo AND            \n";
        $stSql .= "     ad.cod_modulo = c.cod_modulo AND         \n";
        $stSql .= "     ad.cod_cadastro = c.cod_cadastro         \n";

        return $stSql;
    }

}
