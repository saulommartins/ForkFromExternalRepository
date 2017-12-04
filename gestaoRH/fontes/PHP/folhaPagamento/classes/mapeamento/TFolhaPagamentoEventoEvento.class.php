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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.EVENTO
    * Data de Criação: 26/08/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Antunez

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.EVENTO
  * Data de Criação: 26/08/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Eduardo Antunez

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoEventoEvento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoEventoEvento()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.evento_evento');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_evento,timestamp');

    $this->AddCampo('cod_evento'            ,'integer'  ,true   ,'',true,'TFolhaPagamentoEvento','cod_evento');
    $this->AddCampo('timestamp'             ,'timestamp',false  ,'',true,false);
    $this->AddCampo('observacao'            ,'varchar'  ,true   ,'250',false,false);
    $this->AddCampo('valor_quantidade'      ,'numeric'  ,false  ,'14.2',false,false);
    $this->AddCampo('unidade_quantitativa'  ,'numeric'  ,false  ,'14.2',false,false);
}

function recuperaUltimoTimeStamp(&$stTimeStamp, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaUltimoTimeStamp().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if (!$obErro->ocorreu) {
        $stTimeStamp = $rsRecordSet->getCampo("timestamp");
    }

    return $obErro;

}

function montaRecuperaUltimoTimeStamp()
{
    $stSql = "SELECT MAX(timestamp) AS timestamp FROM folhapagamento.evento_evento ";

    return $stSql;
}

}
