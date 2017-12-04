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
    * Classe de mapeamento da tabela licitacao.certificacao_documentos
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 21074 $
    $Name$
    $Author: bruce $
    $Date: 2007-03-15 15:46:19 -0300 (Qui, 15 Mar 2007) $

    * Casos de uso: uc-03.05.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.certificacao_documentos
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoCertificacaoDocumentos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoCertificacaoDocumentos()
{
    parent::Persistente();
    $this->setTabela("licitacao.certificacao_documentos");

    $this->setCampoCod('');
    $this->setComplementoChave('num_certificacao,exercicio,cod_documento,cgm_fornecedor');

    $this->AddCampo('num_certificacao','integer',false ,''   ,true,'TLicitacaoParticipanteCertificacao');
    $this->AddCampo('exercicio'       ,'char'   ,false ,'4'  ,true,'TLicitacaoParticipanteCertificacao');
    $this->AddCampo('cod_documento'   ,'integer',false ,''   ,true,'TLicitacaoDocumento');
    $this->AddCampo('cgm_fornecedor'  ,'integer',false ,''   ,true,'TLicitacaoParticipanteCertificacao');
    $this->AddCampo('num_documento'   ,'varchar',false ,'30' ,false,false);
    $this->AddCampo('dt_emissao'      ,'date'   ,false ,''   ,false,false);
    $this->AddCampo('dt_validade'     ,'date'   ,false ,''   ,false,false);

}

function recuperaDocumentos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDocumentos().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDocumentos()
{
    $stSql  = "  select                                          \n";
    $stSql .= "  	 lcd.num_certificacao                        \n";
    $stSql .= "  	,lcd.exercicio                               \n";
    $stSql .= "  	,lcd.cod_documento                           \n";
    //$stSql .= "  	,lcd.cgm_fornecedor                          \n";
    $stSql .= "      ,to_char(lcd.dt_emissao, 'dd/mm/yyyy' ) as dt_emissao   \n";
    $stSql .= "      ,to_char(lcd.dt_validade, 'dd/mm/yyyy' ) as dt_validade \n";
    $stSql .= "      ,lcd.num_documento                           \n";
    $stSql .= "  	,ld.nom_documento                            \n";
    $stSql .= "  from                                            \n";
    $stSql .= "  	 licitacao.certificacao_documentos as lcd    \n";
    $stSql .= "  	,licitacao.documento as ld                   \n";
    $stSql .= "  where                                           \n";
    $stSql .= "  	lcd.cod_documento = ld.cod_documento         \n";

    return $stSql;
}

function recuperaFiltroDocumentos()
{
    $stFiltro = '';
    if ( $this->getDado( 'num_certificacao' ) )
        $stFiltro.= "AND lcd.num_certificacao = ".$this->getDado( 'num_certificacao' );
    if ( $this->getDado( 'exercicio' ) )
        $stFiltro.= " AND lcd.exercicio = '".$this->getDado('exercicio')."' \n";

    return $stFiltro;
}

}
