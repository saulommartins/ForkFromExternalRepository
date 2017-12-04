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
    * Classe de mapeamento da tabela tceal.pagamento_tipo_documento
    * Data de Criação: 28/05/2014

    * @author Analista: Gelson
    * @author Desenvolvedor: Evandro Noguez Melos

    * @package URBEM
    * @subpackage Mapeamento

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEALPagamentoTipoDocumento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCEALPagamentoTipoDocumento()
{
    parent::Persistente();
    $this->setTabela("tceal.pagamento_tipo_documento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_entidade,exercicio,cod_nota,cod_tipo_documento,timestamp,num_documento');

    $this->AddCampo('cod_tipo_documento'        , 'integer'  , true, ''  , true , true );
    $this->AddCampo('cod_entidade'              , 'integer'  , true, ''  , true , true );
    $this->AddCampo('exercicio'                 , 'varchar'  , true, '04', true , true );
    $this->AddCampo('timestamp'                 , 'timestamp', true, ''  , true , true );
    $this->AddCampo('cod_nota'                  , 'integer'  , true, ''  , true , true );
    $this->AddCampo('num_documento'             , 'varchar'  , true, '15', true , true );
}

public function recuperaCheque(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaCheque().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaCheque(){
    $stSql = "
                SELECT cod_tipo_documento
                        ,cod_entidade
                        ,exercicio
                        ,max(timestamp)
                        ,cod_nota
                        ,num_documento
                FROM tceal.pagamento_tipo_documento 
                
                WHERE exercicio     = '".$this->getDado('exercicio')."' 
                AND cod_nota        = ".$this->getDado('cod_nota')."
                AND cod_entidade    = ".$this->getDado('cod_entidade')."
                
                GROUP BY
                    cod_tipo_documento
                    ,cod_entidade
                    ,exercicio  
                    ,cod_nota
                    ,num_documento
            ";


    return $stSql;
}

}
