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
	* Classe de mapeamento da tabela tcemg.contrato_aditivo
	* Data de Criação   : 28/03/2014

	* @author Analista      Sergio Luiz dos Santos
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: TTCEMGContratoAditivo.class.php 59719 2014-09-08 15:00:53Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGContratoAditivo extends Persistente
{
/**
	* Método Construtor
	* @access Private
*/
function TTCEMGContratoAditivo()
{
    parent::Persistente();
    $this->setTabela('tcemg.contrato_aditivo');
    
    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,cod_contrato_aditivo,exercicio,cod_entidade,exercicio_contrato,cod_entidade_contrato');
    
    $this->AddCampo( 'cod_contrato_aditivo' , 'integer' , true  , ''    , true  , false );
    $this->AddCampo( 'cod_contrato'         , 'integer' , true  , ''    , false , true  );
    $this->AddCampo( 'exercicio_contrato'   , 'char'    , true  , '4'   , false , true  );
    $this->AddCampo( 'cod_entidade_contrato', 'integer' , true  , ''    , false , true  );
    $this->AddCampo( 'nro_aditivo'          , 'integer' , true  , ''    , false , false );
    $this->AddCampo( 'exercicio'            , 'char'    , true  , '4'   , true  , false );
    $this->AddCampo( 'cod_entidade'         , 'integer' , true  , ''    , true  , false );
    $this->AddCampo( 'num_orgao'            , 'integer' , false , ''    , false , true  );
    $this->AddCampo( 'num_unidade'          , 'integer' , false , ''    , false , true  );
    $this->AddCampo( 'data_assinatura'      , 'date'    , true  , ''    , false , false );
    $this->AddCampo( 'cod_tipo_valor'       , 'integer' , true  , ''    , false , false );
    $this->AddCampo( 'cod_tipo_aditivo'     , 'integer' , true  , ''    , false , true  );
    $this->AddCampo( 'descricao'            , 'varchar' , false , '250' , false , false );
    $this->AddCampo( 'valor'                , 'numeric' , true  , '14,2', false , false );
    $this->AddCampo( 'data_termino'         , 'date'    , false , ''    , false , false );
    $this->AddCampo( 'data_publicacao'      , 'date'    , true  , ''    , false , false );
    $this->AddCampo( 'cgm_publicacao'       , 'integer' , true  , ''    , false , true  );
}

function recuperaProximoContratoAditivo(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    
    $stSql = $this->montaRecuperaProximoContratoAditivo();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
    
    return $obErro;
}

function montaRecuperaProximoContratoAditivo()
{
    $stSql  = " SELECT max(cod_contrato_aditivo) + 1 as cod_contrato_aditivo    \n";
    $stSql .= " FROM tcemg.contrato_aditivo                           	    \n";
    
    return $stSql;
}

    public function recuperaContratoAditivo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContratoAditivo().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaContratoAditivo()
    {
        $stSql = "SELECT 
            contrato.cod_entidade,
            contrato.nro_contrato,
            contrato.exercicio,
            contrato.objeto_contrato,
            TCA.nro_aditivo,
            TCA.exercicio AS exercicio_aditivo,
            to_char(TCA.data_assinatura, 'dd/mm/yyyy') AS data_assinatura
            FROM tcemg.contrato_aditivo AS TCA
            INNER JOIN tcemg.contrato
            ON TCA.cod_contrato=contrato.cod_contrato
            AND TCA.exercicio_contrato=contrato.exercicio
            AND TCA.cod_entidade_contrato=contrato.cod_entidade
        ";

        return $stSql;
    }
	
	public function __destruct(){}

}
?>
