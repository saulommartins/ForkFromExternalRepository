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
     * Classe de mapeamento para a tabela IMOBILIARIO.VW_LOCALIZACAO_ATIVO
     * Data de Criação: 30/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerir

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: VCIMLocalizacaoAtiva.class.php 63826 2015-10-21 16:39:23Z arthur $

     * Casos de uso: uc-05.01.03
*/

/*
$Log$
Revision 1.5  2006/09/18 09:12:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class VCIMLocalizacaoAtiva extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela('imobiliario.vw_localizacao_ativa');

    $this->setCampoCod('cod_lote');
    $this->setComplementoChave('');

    $this->AddCampo('cod_nivel',       'integer', true,'' ,true, false);
    $this->AddCampo('cod_vigencia',    'integer', true,'' ,true, false);
    $this->AddCampo('cod_localizacao', 'integer', true,'' ,true, false);
    $this->AddCampo('valor_composto',  'string', true,'' ,true, false);
    $this->AddCampo('valor_reduzido',  'string', true,'' ,true, false);
    $this->AddCampo('valor',           'string', true,'' ,true, false);
    $this->AddCampo('nom_localizacao', 'string', true,'' ,true, false);
    $this->AddCampo('mascara',         'string', true,'' ,true, false);
    $this->AddCampo('nom_nivel',       'string', true,'' ,true, false);
}

function recuperaUltimoValorComposto(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUltimoValorComposto().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    return $obErro;
}
    
function montaRecuperaUltimoValorComposto(){
    
    $stSql = " SELECT (COALESCE( SPLIT_PART(MAX(valor_composto), '.', ".$this->getDado("cod_nivel").")::INTEGER, 0) + 1) AS codigo_localizacao
                 FROM imobiliario.vw_localizacao_ativa ";
    
    return $stSql;
}

public function __destruct(){}

}

?>