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
    * Classe de mapeamento da tabela CONTABILIDADE.HISTORICO_CONTABIL
    * Data de Criação: 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-02-12 16:17:27 -0200 (Seg, 12 Fev 2007) $

    * Casos de uso: uc-02.02.03
                    uc-02.02.20
*/

/*
$Log$
Revision 1.7  2007/02/12 18:17:27  luciano
#8371#

Revision 1.6  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CONTABILIDADE.HISTORICO_CONTABIL
  * Data de Criação: 01/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadeHistoricoContabil extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeHistoricoContabil()
{
    parent::Persistente();
    $this->setTabela('contabilidade.historico_contabil');

    $this->setCampoCod('cod_historico');
    $this->setComplementoChave('cod_historico,exercicio');

    $this->AddCampo('cod_historico','integer',true,'',true,false);
    $this->AddCampo('exercicio','char',true,'04',true,false);
    $this->AddCampo('nom_historico','varchar',true,'80',false,false);
    $this->AddCampo('complemento','boolean',true,'',false,false);
    $this->AddCampo('historico_interno','boolean',false,'',false,false);

}

function recuperaHistoricosInclusao(&$inCodigo, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaHistoricosInclusao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $inCodigo, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaHistoricosInclusao()
{
    $stSQL  = "SELECT MAX(cod_historico) as cod_historico
                FROM contabilidade.historico_contabil
                WHERE historico_contabil.cod_historico BETWEEN 1 AND 799";

   return $stSQL;

}

}
