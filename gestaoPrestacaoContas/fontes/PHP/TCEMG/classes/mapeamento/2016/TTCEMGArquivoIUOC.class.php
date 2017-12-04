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
    * Classe de mapeamento da tabela tcemg.arquivo_iuoc
    * Data de Criação: 19/05/2014
    
    
    * @author Desenvolvedor: Franver Sarmento de Moraes
    
    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: TTCEMGArquivoIUOC.class.php 62269 2015-04-15 18:28:39Z franver $
*/

include_once( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php" );

class TTCEMGArquivoIUOC extends Persistente {
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEMGArquivoIUOC()
    {
        parent::Persistente();
        $this->setTabela('tcemg.arquivo_iuoc');
        
        $this->setCampoCod('');
        $this->setComplementoChave('num_orgao, num_unidade, exercicio, mes');
        
        $this->AddCampo('num_orgao'  , 'integer', true, '', true, true);
        $this->AddCampo('num_unidade', 'integer', true, '', true, true);
        $this->AddCampo('exercicio'  , 'char'   , true,'4', true, true);
        $this->AddCampo('mes'        , 'integer', true, '', true, false);
    }
    
    public function __destruct(){}

}
?>