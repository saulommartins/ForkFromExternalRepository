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
    * Classe de mapeamento da tabela PESSOAL.CONTRATO_SERVIDOR_REGIME_FUNCAO
    * Data de Criação: 22/08/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-12-13 13:46:10 -0200 (Qui, 13 Dez 2007) $

    * Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CONTRATO_SERVIDOR_REGIME_FUNCAO
  * Data de Criação: 22/08/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorRegimeFuncao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorRegimeFuncao()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_regime_funcao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_regime,cod_contrato,timestamp');

    $this->AddCampo('cod_regime','integer',true,'',true,true);
    $this->AddCampo('cod_contrato','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                                                       \n";
    $stSql .= "     pf.*                                                                    \n";
    $stSql .= "FROM                                                                         \n";
    $stSql .= "     pessoal.contrato_servidor_regime_funcao as pf,                      \n";
    $stSql .= "     ( SELECT                                                                \n";
    $stSql .= "         cod_contrato,                                                       \n";
    $stSql .= "         max(timestamp) as timestamp                                         \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         pessoal.contrato_servidor_regime_funcao                         \n";
    $stSql .= "     GROUP BY                                                                \n";
    $stSql .= "         cod_contrato                                                        \n";
    $stSql .= "     ) as max                                                                \n";
    $stSql .= "WHERE                                                                        \n";
    $stSql .= "         pf.cod_contrato    = max.cod_contrato                               \n";
    $stSql .= "     AND pf.timestamp       = max.timestamp                                  \n";

    return $stSql;
}

function recuperaRegimeDeContratos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaRegimeDeContratos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaRegimeDeContratos()
{
    $stSql .= "SELECT contrato_servidor_regime_funcao.*                                                                                                                        \n";
    $stSql .= "     , (SELECT descricao FROM pessoal.regime WHERE regime.cod_regime = contrato_servidor_regime_funcao.cod_regime) as descricao       \n";
    $stSql .= "  FROM pessoal.contrato_servidor_regime_funcao                                                                                        \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                                                                                   \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                                                    \n";
    $stSql .= "            FROM pessoal.contrato_servidor_regime_funcao                                                                              \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contratro_servidor_regime_funcao                                                                                          \n";
    $stSql .= " WHERE contrato_servidor_regime_funcao.cod_contrato = max_contratro_servidor_regime_funcao.cod_contrato                                                         \n";
    $stSql .= "   AND contrato_servidor_regime_funcao.timestamp = max_contratro_servidor_regime_funcao.timestamp                                                               \n";

    return $stSql;
}

}
