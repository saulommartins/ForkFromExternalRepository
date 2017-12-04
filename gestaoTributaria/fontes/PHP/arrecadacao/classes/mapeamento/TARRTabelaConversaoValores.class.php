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
/*
    * Classe de regra de mapeamento para arrecadacao.tabela_conversao_valores
    * Data de Criacao: 11/09/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Vitor Hugo
    * @ignore

    * $Id: TARRTabelaConversaoValores.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.23
*/

/*
$Log$
Revision 1.1  2007/09/13 13:39:24  vitor
uc-05.03.23

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TARRTabelaConversaoValores extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
function TARRTabelaConversaoValores()
{
    parent::Persistente();
    $this->setTabela( 'arrecadacao.tabela_conversao_valores' );

    $this->setCampoCod( 'cod_tabela' );
    $this->setComplementoChave( 'exercicio, parametro_1, parametro_2, parametro_3, parametro_4' );

                      //nome,      tipo     req. Tam. PK  FK
    $this->AddCampo( 'cod_tabela','integer',true,'',true,true );
    $this->AddCampo( 'exercicio','varchar',true,'4',true,true );
    $this->AddCampo( 'parametro_1','varchar',true,'50',true,false );
    $this->AddCampo( 'parametro_2','varchar',true,'30',true,false );
    $this->AddCampo( 'parametro_3','varchar',true,'30',true,false );
    $this->AddCampo( 'parametro_4','varchar',true,'30',true,false );
    $this->AddCampo( 'valor','varchar',true,'30',false,false );
}

function recuperaListaTabelaConversaoValores(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaListaTabelaConversaoValores().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaTabelaConversaoValores()
{
    $stSql ="   SELECT                                     \n";
    $stSql.="      parametro_1,                            \n";
    $stSql.="      parametro_2,                            \n";
    $stSql.="      parametro_3,                            \n";
    $stSql.="      parametro_4,                            \n";
    $stSql.="      valor                                   \n";
    $stSql.="   FROM                                       \n";
    $stSql.="      arrecadacao.tabela_conversao_valores    \n";

    return $stSql;
}

}
