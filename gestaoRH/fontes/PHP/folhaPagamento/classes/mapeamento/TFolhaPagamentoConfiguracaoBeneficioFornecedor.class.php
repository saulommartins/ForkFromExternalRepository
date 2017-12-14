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
  * Efetua conexão com a tabela  folhapagamento.configuracao_beneficio_fornecedor
  * Data de Criação: 18/03/2015

  * @author Analista: Dagiane 
  * @author Desenvolvedor: Carlos Adriano

  * @package URBEM
  * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFolhaPagamentoConfiguracaoBeneficioFornecedor extends Persistente
{

/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoConfiguracaoBeneficioFornecedor()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.configuracao_beneficio_fornecedor");

    $this->setCampoCod('cod_configuracao');
    $this->setComplementoChave('timestamp');

    $this->AddCampo('cod_configuracao','sequence'  , true , '', true, false);
    $this->AddCampo('timestamp'       ,'timestamp' , false, '', true, false);
    $this->AddCampo('cgm_fornecedor'  ,'integer'   , true , '', true, false);
}

function montaRecuperaRelacionamento()
{
    $stSql .= " SELECT evento.codigo
                     , evento.descricao
                     , sw_cgm.numcgm
                     , sw_cgm.nom_cgm
                  FROM folhapagamento.configuracao_beneficio
                  JOIN folhapagamento.configuracao_beneficio_fornecedor
                    ON configuracao_beneficio_fornecedor.cod_configuracao = configuracao_beneficio.cod_configuracao
                   AND configuracao_beneficio_fornecedor.timestamp 	  = configuracao_beneficio.timestamp
                  JOIN sw_cgm
                    ON sw_cgm.numcgm = configuracao_beneficio_fornecedor.cgm_fornecedor
                  JOIN folhapagamento.beneficio_evento
                    ON beneficio_evento.cod_configuracao = configuracao_beneficio.cod_configuracao
                   AND beneficio_evento.timestamp 	  = configuracao_beneficio.timestamp
                  JOIN folhapagamento.evento 
                    ON evento.cod_evento = beneficio_evento.cod_evento
                 WHERE configuracao_beneficio.cod_configuracao > 1
              ";

    return $stSql;
}
}