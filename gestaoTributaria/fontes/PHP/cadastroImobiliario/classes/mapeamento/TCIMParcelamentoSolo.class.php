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
     * Classe de mapeamento para a tabela IMOBILIARIO.PARCELAMENTO_SOLO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMParcelamentoSolo.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.5  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.PARCELAMENTO_SOLO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMParcelamentoSolo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMParcelamentoSolo()
{
    parent::Persistente();
    $this->setTabela('imobiliario.parcelamento_solo');

    $this->setCampoCod('cod_parcelamento');
    $this->setComplementoChave('');

    $this->AddCampo('cod_parcelamento','integer',true,'',true,false);
    $this->AddCampo('cod_lote','integer',true,'',false,true);
    $this->AddCampo('cod_tipo','integer',true,'',false,true);
    $this->AddCampo('dt_parcelamento','date',true,'',false,false);

}

function recuperaLotesParcelados(&$rsRecordset, $stFitro='')
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    $stSql  = $this->montaRecuperaLotesParcelados().$stFitro;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordset,$stSql, "" );

return $obErro;
}

function montaRecuperaLotesParcelados()
{
    $stSql = "SELECT cod_tipo
                   , cod_parcelamento
                   , valor as lote_origem
                   , to_char(dt_parcelamento, 'dd/mm/yyyy') as dt_parcelamento
                   , nom_tipo

                   , array_to_string(
                        ARRAY( SELECT valor
                                 FROM imobiliario.lote_parcelado
                                LEFT JOIN imobiliario.lote_localizacao
                                    USING(cod_lote)

                                WHERE cod_parcelamento = ips.cod_parcelamento
                                ORDER BY cod_lote
                             ), ', '
                                   ) as lotes
                FROM imobiliario.parcelamento_solo as ips
                   LEFT JOIN imobiliario.lote_localizacao
                     USING( cod_lote)
                   LEFT JOIN imobiliario.tipo_parcelamento
                     USING( cod_tipo)  \n";
return $stSql;
}
}
