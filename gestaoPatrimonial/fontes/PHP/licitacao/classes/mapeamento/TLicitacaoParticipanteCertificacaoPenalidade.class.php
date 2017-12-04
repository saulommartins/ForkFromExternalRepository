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
    * Classe de mapeamento da tabela licitacao.participante_penalidade
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 17847 $
    $Name$
    $Author: tonismar $
    $Date: 2006-11-20 07:46:49 -0200 (Seg, 20 Nov 2006) $

    * Casos de uso: uc-03.05.28
*/
/*
$Log$
Revision 1.3  2006/11/20 09:46:49  tonismar
bug #6700#

Revision 1.2  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.1  2006/10/12 11:51:50  tonismar
ManterPenalidadeFornecedor

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.participante_penalidade
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoParticipanteCertificacaoPenalidade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoParticipanteCertificacaoPenalidade()
{
    parent::Persistente();
    $this->setTabela("licitacao.participante_certificacao_penalidade");

    $this->setCampoCod('');
    $this->setComplementoChave('num_certificacao','exercicio','cgm_fornecedor');

    $this->AddCampo('num_certificacao','integer',false,'',false,'TLicitacaoParticipanteCertificacao');
    $this->AddCampo('exercicio'   ,'varchar',false ,'',false,'TLicitacaoParticipanteCertificacao');
    $this->AddCampo('cgm_fornecedor','integer',false ,'',false,true);
    $this->AddCampo('cod_documento' ,'integer',false ,'',false,'TAdministracaoModeloDocumento');
    $this->AddCampo('cod_tipo_documento' ,'integer',false ,'',false,'TAdministracaoModeloDocumento');
}

function recuperaListaPenalidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaPenalidade().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaPenalidade()
{
    $stSql .= "  select											\n";
    $stSql .= "  	 lpcp.num_certificacao                         \n";
    $stSql .= "  	,lpcp.exercicio                                \n";
    $stSql .= "  	,lpcp.cgm_fornecedor                           \n";
    $stSql .= "  	,lpcp.cod_tipo_documento                       \n";
    $stSql .= "  	,lpcp.cod_documento                            \n";
    $stSql .= "  	,cgm.nom_cgm                                  \n";
    $stSql .= "      ,amd.nome_documento                            \n";
    $stSql .= "  from                                              \n";
    $stSql .= "  	 licitacao.participante_certificacao_penalidade as lpcp     \n";
    $stSql .= "  	,administracao.modelo_documento as amd        \n";
    $stSql .= "  	,sw_cgm as cgm                                \n";
    $stSql .= "  where                                             \n";
    $stSql .= "  	lpcp.cgm_fornecedor = cgm.numcgm              \n";
    $stSql .= "      and lpcp.cod_documento = amd.cod_documento    \n";
    $stSql .= "      and lpcp.cod_tipo_documento = amd.cod_tipo_documento \n";

    if ( $this->getDado( 'num_certificacao' ) ) {
        $stSql .= " and lpcp.num_certificacao = ".$this->getDado( 'num_certificacao' )." \n";
    }

    if ( $this->getDado( 'cgm_fornecedor' ) ) {
        $stSql .= " and lpcp.cgm_fornecedor = ".$this->getDado( 'cgm_fornecedor' )." \n";
    }

    return $stSql;
}

}
