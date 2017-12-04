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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_CGM
    * Data de Criação: 28/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaCGM.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.3  2006/09/29 17:15:52  dibueno
Alteração nos parametros dos campos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATDividaCGM extends Persistente
{
    public $inExercicio    ;
    public $inCodInscricao ;
    public $inInscricaoMunicipal   ;

    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaCGM()
    {
        parent::Persistente();
        $this->setTabela('divida.divida_cgm');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_inscricao, numcgm, exercicio');

        $this->AddCampo('exercicio','varchar',true,'4',true,true);
        $this->AddCampo('cod_inscricao','integer',true,'',true,true);
        $this->AddCampo('numcgm','integer',true,'',true,true);

        $this->inExercicio    		= '0';
        $this->inCodInscricao 		= '0';
        $this->inInscricaoMunicipal	= '0';

    }

}// end of class

?>
