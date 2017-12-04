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
    * Classe de mapeamento da tabela ARRECADACAO.PERMISSAO_CANCELAMENTO
    * Data de Criação: 26/07/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRPermissaoCancelamento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.1  2007/07/27 13:15:14  cercato
Bug#9762#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRPermissaoCancelamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRPermissaoCancelamento()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.permissao_cancelamento');

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm');

    $this->AddCampo('numcgm', 'integer',true, '', true, true);
    $this->AddCampo('timestamp', 'timestamp', false, '', true, false );
}

function recuperaListaCGMs(&$rsRecordSet, $stCondicao, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaListaCGMs().$stCondicao;
    $this->setDebug( $stSql );
    #$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaCGMs()
{
    $stSql  = " SELECT
                    numcgm,
                    (
                        SELECT
                            nom_cgm
                        FROM
                            sw_cgm
                        WHERE
                            sw_cgm.numcgm = permissao_cancelamento.numcgm
                    )AS nom_cgm
                FROM
                    arrecadacao.permissao_cancelamento
                WHERE
                    permissao_cancelamento.timestamp = (    SELECT
                                                                timestamp
                                                            FROM
                                                                arrecadacao.permissao_cancelamento
                                                            ORDER BY
                                                                timestamp DESC
                                                            LIMIT 1 )";

    return $stSql;
}

}
?>
