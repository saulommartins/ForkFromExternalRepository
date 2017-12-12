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
  * Classe de mapeamento da tabela pessoal.contrato_servidor_situacao
  * Data de Criação: 25/07/2016

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Evandro Melos

  * @package URBEM
  * @subpackage Mapeamento
  
*/

include_once ( CLA_PERSISTENTE );

class TPessoalContratoServidorSituacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_situacao');

    $this->setCampoCod('cod_contrato');
    $this->setComplementoChave('timestamp, situacao');

    $this->AddCampo('cod_contrato'              ,'integer'       ,true,''  ,true,false);
    $this->AddCampo('situacao'                  ,'char'          ,true,'1' ,true,false);
    $this->AddCampo('cod_periodo_movimentacao'  ,'integer'       ,true,''  ,false,false);    
    $this->AddCampo('situacao_literal'          ,'varchar'       ,true,'25',false,false);    
    $this->AddCampo('deleted'                   ,'boolean'       ,true,''  ,false,false);
}


}
