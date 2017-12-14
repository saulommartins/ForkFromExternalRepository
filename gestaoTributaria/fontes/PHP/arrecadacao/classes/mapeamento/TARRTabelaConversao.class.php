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
    * Classe de regra de mapeamento para arrecadacao.tabela_conversao
    * Data de Criacao: 06/09/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Vitor Hugo
    * @ignore

    * $Id: TARRTabelaConversao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.23
*/

/*
$Log$
Revision 1.1  2007/09/13 13:39:15  vitor
uc-05.03.23

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TARRTabelaConversao extends Persistente
{
    /**
        * Metodo Construtor
        * @access Private
    */
    public function TARRTabelaConversao()
    {
        parent::Persistente();
        $this->setTabela( 'arrecadacao.tabela_conversao' );

        $this->setCampoCod( 'cod_tabela' );
        $this->setComplementoChave( 'cod_tabela, exercicio' );

                        //nome,      tipo     req. Tam. PK  FK
        $this->AddCampo( 'cod_tabela','integer',true,'',true,false );
        $this->AddCampo( 'exercicio','varchar',true,'4',true,false );
        $this->AddCampo( 'cod_modulo','integer',true,'',false,true );
        $this->AddCampo( 'nome_tabela','varchar',true,'80',false,false );
        $this->AddCampo( 'parametro_1','varchar',true,'80',false,false );
        $this->AddCampo( 'parametro_2','varchar',true,'80',false,false );
        $this->AddCampo( 'parametro_3','varchar',true,'80',false,false );
        $this->AddCampo( 'parametro_4','varchar',true,'80',false,false );
    }

    public function recuperaListaTabelaConversao(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaTabelaConversao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaTabelaConversao()
    {
        $stSql ="   SELECT                                     \n";
        $stSql.="      cod_tabela,                             \n";
        $stSql.="      nome_tabela,                            \n";
        $stSql.="      exercicio,                              \n";
        $stSql.="      cod_modulo,                             \n";
        $stSql.="      parametro_1,                            \n";
        $stSql.="      parametro_2,                            \n";
        $stSql.="      parametro_3,                            \n";
        $stSql.="      parametro_4                             \n";
        $stSql.="   FROM                                       \n";
        $stSql.="      arrecadacao.tabela_conversao            \n";

        return $stSql;
    }

    public function recuperaListaExercicioTabelaConversao(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaExercicioTabelaConversao();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaExercicioTabelaConversao()
    {
        $stSql = "SELECT distinct
                    exercicio
                from
                    arrecadacao.tabela_conversao\n";

        return $stSql;
    }

}
