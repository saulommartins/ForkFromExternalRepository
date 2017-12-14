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
    * Classe de mapeamento para ARRECADACAO.PARCELAMENTO_LANCAMENTO
    * Data de Criação: 29/03/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRParcelamentoLancamento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.20
*/

/*
$Log$
Revision 1.2  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TARRParcelamentoLancamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRParcelamentoLancamento()
{
    parent::Persistente();
    $this->setTabela("arrecadacao.parcelamento_lancamento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lancamento, cod_parcela');

    $this->AddCampo('cod_lancamento','integer',true,'',true,true);
    $this->AddCampo('cod_parcela','integer',true,'',true,true);

}

function montaRecuperaRelacionamento()
{
    $stSql = "SELECT                                                  \n";
    $stSql = "  apl.cod_lancamento,                           \n";
    $stSql = "  apl.cod_parcelamo                              \n";

    $stSql = "FROM                                                     \n";
    $stSql = "  arrecadacao.parcelamento_lancamento as apl   \n";
    $stSql = "INNER JOIN                                             \n";
    $stSql = "  arrecadacao.lancamento as al           \n";
    $stSql = "ON                                                        \n";
    $stSql = "  al.cod_lancamento = apl.cod_lancamento           \n";

    $stSql = "INNER JOIN                                            \n";
    $stSql = "  arrecadacao.parcela as ap                \n";
    $stSql = "ON                                                        \n";
    $stSql = "  ap.cod_parcela = apl.cod_parcela     \n";

return $stSql;
}

}
