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
  * Classe de mapeamento da tabela PESSOAL.CONTRATO_SERVIDOR_PADRAO
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CONTRATO_SERVIDOR_PADRAO
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorPadrao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorPadrao()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_padrao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,cod_padrao,timestamp');

    $this->AddCampo('cod_contrato'      ,'integer'      ,true,''    ,true,true);
    $this->AddCampo('cod_padrao'        ,'integer'      ,true,''    ,true,true);
    $this->AddCampo('timestamp'         ,'timestamp'    ,false,''   ,true,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT                                           \n";
    $stSql .= "     *                                           \n";
    $stSql .= "FROM                                             \n";
    $stSql .= "     pessoal.contrato_servidor_padrao as pp, \n";
    $stSql .= "     folhapagamento.padrao            as fp  \n";
    $stSql .= "WHERE                                            \n";
    $stSql .= "     pp.cod_padrao = fp.cod_padrao               \n";

    return $stSql;
}

function recuperaPadraoServidor(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaPadraoServidor",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaPadraoServidor()
{
    $stSql  = "    SELECT *                                                                             \n";
    $stSql .= "      FROM pessoal.contrato                                                              \n";
    $stSql .= "INNER JOIN ultimo_contrato_servidor_padrao('".Sessao::getEntidade()."',".$this->getDado("inCodPeriodoMovimentacao").") as ultimo_contrato_servidor_padrao \n";
    $stSql .= "        ON contrato.cod_contrato = ultimo_contrato_servidor_padrao.cod_contrato          \n";
    $stSql .= "INNER JOIN ultimo_contrato_servidor_funcao('".Sessao::getEntidade()."',".$this->getDado("inCodPeriodoMovimentacao").") as ultimo_contrato_servidor_funcao \n";
    $stSql .= "        ON contrato.cod_contrato = ultimo_contrato_servidor_funcao.cod_contrato          \n";

    if ($this->getDado("stSituacoContrato")) {
        $stSql .= "     WHERE recuperarSituacaoDoContrato(contrato.cod_contrato,".$this->getDado("inCodPeriodoMovimentacao").",'".Sessao::getEntidade()."') IN (".$this->getDado("stSituacoContrato").") \n";
    }

    return $stSql;
}

}
