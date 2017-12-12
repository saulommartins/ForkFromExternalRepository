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
     * Classe de mapeamento para a tabela IMOBILIARIO.TRANSFERENCIA_ADQUIRENTE
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMTransferenciaAdquirente.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.17
*/

/*
$Log$
Revision 1.5  2006/09/18 09:12:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.TRANSFERENCIA_ADQUIRENTE
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMTransferenciaAdquirente extends Persistente
{
var $inscricao;
var $cod_lancamento;
/**
    * Método Construtor
    * @access Private
*/
function TCIMTransferenciaAdquirente()
{
    parent::Persistente();
    $this->setTabela('imobiliario.transferencia_adquirente');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_transferencia,numcgm');

    $this->AddCampo('cod_transferencia','integer',true,'',true,true);
    $this->AddCampo('numcgm','integer',true,'',true,true);
    $this->AddCampo('ordem','integer',true,'',false,false);
    $this->AddCampo('cota','numeric',true,'5,2',false,false);

}

function recuperaAdquirentes(&$rsRecordSet, $stFiltro = "", $stGroup = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAdquirentes().$stFiltro.$stGroup.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAdquirentes()
{
    $stSql = "SELECT transferencia_adquirente.cod_transferencia,
                     transferencia_adquirente.numcgm AS numcgm_adquirente,
                     transferencia_adquirente.ordem,
                     transferencia_adquirente.cota,
                     sw_cgm.nom_cgm AS cgm_adquirente
            FROM
                 arrecadacao.lancamento_calculo
            JOIN arrecadacao.calculo
              ON calculo.cod_calculo = lancamento_calculo.cod_calculo

            JOIN imobiliario.transferencia_imovel
              ON transferencia_imovel.dt_cadastro = calculo.timestamp

            JOIN imobiliario.transferencia_adquirente
              ON transferencia_adquirente.cod_transferencia = transferencia_imovel.cod_transferencia

            JOIN sw_cgm
              ON sw_cgm.numcgm = transferencia_adquirente.numcgm

            WHERE lancamento_calculo.cod_lancamento = ".$this->cod_lancamento."";

    return $stSql;
}

function setInscricao($inscricao)
{
    $this->inscricao = $inscricao;
}

function setCodLancamento($cod_lancamento)
{
    $this->cod_lancamento = $cod_lancamento;
}

}
