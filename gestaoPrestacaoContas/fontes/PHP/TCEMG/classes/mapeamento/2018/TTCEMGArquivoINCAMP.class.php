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
    * Classe de mapeamento da tabela tcemg.arquivo_incamp
    * Data de Criação: 19/05/2014
    
    
    * @author Desenvolvedor: Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id:  $
*/

include_once( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php" );

class TTCEMGArquivoINCAMP extends Persistente {
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEMGArquivoINCAMP()
    {
        parent::Persistente();
        $this->setTabela('tcemg.arquivo_incamp');
        
        $this->setCampoCod('');
        $this->setComplementoChave('cod_acao, exercicio, mes');
        
        $this->AddCampo('cod_acao' , 'integer', true, '', true, true);
        $this->AddCampo('exercicio', 'char'   , true,'4', true, false);
        $this->AddCampo('mes'      , 'integer', true, '', true, false);
    }
    
    public function __destruct(){}

}
?>