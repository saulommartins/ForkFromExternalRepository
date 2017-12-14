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
    * Classe de Mapeamento da tabela tcemg.obs_meta_arrecadacao
    * Data de Criação   : 04/02/2015

    * @author Analista      
    * @author Desenvolvedor Lisiane Morais

    * @package URBEM
    * @subpackage

    $Id:$
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TTCEMGItemAtivoPassivo extends Persistente
{
function TTCEMGItemAtivoPassivo()
{
    parent::Persistente();

    $this->setCampoCod('mes');
    $this->setComplementoChave('exercicio');

    $this->AddCampo( 'mes'         , 'integer', true  , '' , true , false);
    $this->AddCampo( 'exercicio'   , 'char'   , true  , '4', true , false);
    $this->AddCampo( 'dataInicial' , 'varchar', false , '' , false , false);
    $this->AddCampo( 'dataFinal'   , 'varchar', false , '' , false , false);
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
                SELECT  '12' AS mes
                        , cod_tipo 
                        , ABS(valor_acrescimo) as valor_acrescimo
                        , ABS(vl_reducao) as vl_reducao
                        , ' '::VARCHAR AS descricao
                FROM tcemg.item_ativo_passivo ('".Sessao::getExercicio()."'
                                                , '".$this->getDado('dataInicial')."'
                                                , '".$this->getDado('dataFinal')."'
                                                , '".$this->getDado('cod_entidade')."'
                                                )
                AS retorno ( 
                            valor_acrescimo NUMERIC 
						    , vl_reducao NUMERIC 
						    , cod_tipo VARCHAR
					       )
                ORDER by cod_tipo
               ";

    return $stSql;
}

public function __destruct(){}


}

?>
