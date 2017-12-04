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
    * Classe de Mapeamento da tabela tcemg.execucao_variacao
    * Data de Criação   : 19/01/2009

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    $Id:$
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TTCEMGExecucaoVariacao extends Persistente
{
function TTCEMGExecucaoVariacao()
{
    parent::Persistente();
    $this->setTabela("tcemg.execucao_variacao");

    $this->setCampoCod('exercicio');
    $this->setCampoCod('cod_mes');

    $this->AddCampo('cod_mes'                   , 'integer', true, ''       , true,  false);
    $this->AddCampo( 'exercicio'                  , 'char'    , true  , '4'    , true  , false  );
    $this->AddCampo( 'cons_adm_dir'           , 'char'    , true  , '4000' , false , false  );
    $this->AddCampo( 'cons_aut'                  , 'char'    , true  , '4000' , false , false  );
    $this->AddCampo( 'cons_fund'                 , 'char'    , true  , '4000' , false , false  );
    $this->AddCampo( 'cons_empe_est_dep' , 'char'    , true  , '4000' , false , false  );
    $this->AddCampo( 'cons_dem_ent'          , 'char'    , true  , '4000' , false , false  );

}

function recuperaDadosArquivo(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosArquivo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosArquivo()
{
    $stSql  = "
        SELECT 12 as mes
             , cod_mes
             , cons_adm_dir
             , cons_aut
             , cons_fund
             , cons_empe_est_dep
             , cons_dem_ent
          FROM tcemg.execucao_variacao
        WHERE exercicio = '".Sessao::getExercicio()."'
             AND cod_mes = ".$this->getDado("cod_mes")."";

    return $stSql;
}


function recuperaDadosBimestre(&$rsRecordSet, $stFiltro = "", $stOrdem = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosArquivoBimestre().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosArquivoBimestre()
{
    $stSql  = "
        SELECT 12 as mes
             , cod_mes
             , cons_adm_dir
             , cons_aut
             , cons_fund
             , cons_empe_est_dep
             , cons_dem_ent
          FROM tcemg.execucao_variacao
         WHERE exercicio = '".Sessao::getExercicio()."'
           AND cod_mes IN (".$this->getDado("cod_mes").") ";

    return $stSql;
}

function recuperaRelacionamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaRelacionamento();
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

function montaRecuperaRelacionamento()
{
        $stSql  = "
                SELECT *
                  FROM  tcemg.execucao_variacao
                WHERE cod_mes = ".$this->getDado("cod_mes")."
                    AND exercicio = '".Sessao::read('exercicio')."'

               ";

        return $stSql;
}

public function __destruct(){}

}

?>
