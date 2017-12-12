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
    * Classe de mapeamento da tabela PESSOAL.CONTRATO_SERVIDOR_NOMEACAO_POSSE
    * Data de Criação: 08/09/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-10 13:40:16 -0300 (Seg, 10 Mar 2008) $

    * Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CONTRATO_SERVIDOR_NOMEACAO_POSSE
  * Data de Criação: 08/09/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorNomeacaoPosse extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorNomeacaoPosse()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_nomeacao_posse');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,timestamp');

    $this->AddCampo('cod_contrato','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('dt_nomeacao','date',true,'',false,false);
    $this->AddCampo('dt_posse','date',true,'',false,false);
    $this->AddCampo('dt_admissao','date',true,'',false,false);

}

function recuperaNomeacaoPosseDeContratos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaNomeacaoPosseDeContratos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaNomeacaoPosseDeContratos()
{
    $stSql .= "SELECT contrato_servidor_nomeacao_posse.cod_contrato                                                                                                                        \n";
    $stSql .= "     , to_char(contrato_servidor_nomeacao_posse.dt_admissao,'dd/mm/yyyy') as dt_admissao                                                                                                                        \n";
    $stSql .= "     , to_char(contrato_servidor_nomeacao_posse.dt_nomeacao,'dd/mm/yyyy') as dt_nomeacao                                                                                                                        \n";
    $stSql .= "     , to_char(contrato_servidor_nomeacao_posse.dt_posse,'dd/mm/yyyy') as dt_posse                                                                                                                        \n";
    $stSql .= "  FROM pessoal.contrato_servidor_nomeacao_posse                                                                                        \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                                                                                   \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                                                    \n";
    $stSql .= "            FROM pessoal.contrato_servidor_nomeacao_posse                                                                              \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contratro_servidor_nomeacao_posse                                                                                          \n";
    $stSql .= " WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contratro_servidor_nomeacao_posse.cod_contrato                                                         \n";
    $stSql .= "   AND contrato_servidor_nomeacao_posse.timestamp = max_contratro_servidor_nomeacao_posse.timestamp                                                               \n";

    return $stSql;
}

function recuperaTotalContratosCaged(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaTotalContratosCaged",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaTotalContratosCaged()
{
    $stSql .= "SELECT count(1) as contador                                                                  \n";
    $stSql .= "  FROM pessoal.contrato_servidor_nomeacao_posse                    \n";
    $stSql .= "     , (  SELECT cod_contrato\n";
    $stSql .= "               , max(timestamp) as timestamp\n";
    $stSql .= "            FROM pessoal.contrato_servidor_nomeacao_posse\n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse       \n";
    $stSql .= " WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato\n";
    $stSql .= "   AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp           \n";
    $stSql .= "   AND NOT EXISTS (SELECT 1\n";
    $stSql .= "                     FROM pessoal.contrato_servidor_caso_causa\n";
    $stSql .= "                    WHERE contrato_servidor_caso_causa.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato\n";
    $stSql .= "                      AND to_char(dt_rescisao,'yyyy-mm') != '".$this->getDado("competencia")."')\n";

    return $stSql;
}

}
