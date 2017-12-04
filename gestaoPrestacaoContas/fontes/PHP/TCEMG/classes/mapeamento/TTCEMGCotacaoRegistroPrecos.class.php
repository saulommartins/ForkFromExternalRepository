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
 * Classe de mapeamento da tabela tcemg.cotacao_registro_precos
 * Data de Criação: 11/03/2014
 * 
 * @author Analista      : Eduardo Schitz
 * @author Desenvolvedor : Franver Sarmento de Moraes
 * 
 * @package URBEM
 * @subpackage Mapeamento
 * 
 * Casos de uso: uc-02.09.04
 *
 * $Id: TTCEMGCotacaoRegistroPrecos.class.php 59719 2014-09-08 15:00:53Z franver $
 * $Revision: 59719 $
 * $Author: franver $
 * $Date: 2014-09-08 12:00:53 -0300 (Mon, 08 Sep 2014) $
 * 
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGCotacaoRegistroPrecos extends Persistente
{
    public function TTCEMGCotacaoRegistroPrecos()
    {
        parent::Persistente();
        $this->setTabela('tcemg.cotacao_registro_precos');
        
        $this->setCampoCod('');
        $this->setComplementoChave('numero_processo_adesao, exercicio_adesao, cod_lote, cod_item');
        
        $this->AddCampo('numero_processo_adesao'    ,'integer',true,'', true, true);
        $this->AddCampo('exercicio_adesao'          ,'varchar',true,'', true, true);
        $this->AddCampo('cod_lote'                  ,'integer',true,'', true, true);
        $this->AddCampo('cod_item'                  ,'integer',true,'', true, true);
        $this->AddCampo('data_cotacao'              ,'date'   ,true,'',false,false);
        $this->AddCampo('vl_cotacao_preco_unitario' ,'numeric',true,'',false,false);
        $this->AddCampo('quantidade_cotacao'        ,'numeric',true,'',false,false);
 
    }
    
    public function __destruct(){}

}
?>