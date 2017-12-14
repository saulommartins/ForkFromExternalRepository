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
    * Componente IMontaNumeroLicitacaoMultiploEntidade
    * Data de Criação: 23/10/2006

    * @author Analista:
    * @author Desenvolvedor: Tonismar Régis Bernardo

    $Revision: 25063 $
    $Name$
    $Author: bruce $
    $Date: 2007-08-24 12:28:20 -0300 (Sex, 24 Ago 2007) $

    * Casos de uso: uc-03.05.16, uc-03.05.15

*/

/*
$Log$
Revision 1.6  2007/08/24 15:28:20  bruce
Bug#9824#

Revision 1.5  2007/08/23 15:25:09  bruce
Bug#9849#

*/

include_once ( CLA_OBJETO );

class IMontaNumeroLicitacaoMultiploEntidadeUsuarioDispensa extends Objeto
{
    public $obForm;
    public $obExercicio;
    public $obISelectMultiploEntidadeUsuario;
    public $obISelectModalidade;
    public $obTxtLicitacao;
    public $stName;
    public $stRotulo;

    public function setRotulo($valor) { $this->stRotulo = $valor; }
    public function setName($valor) { $this->stName   = $valor; }

    public function getRotulo() { return $this->stRotulo; }
    public function getName() { return $this->stNme;    }

    public function IMontaNumeroLicitacaoMultiploEntidadeUsuarioDispensa(&$obForm)
    {
        parent::Objeto();

        $rsLicitacao = new RecordSet();

        include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );
        include_once ( CAM_GP_COM_COMPONENTES."ISelectModalidade.class.php" );

        $this->obExercicio = new Exercicio();
        $this->obExercicio->setName( 'stExercicioLicitacao' );
        $this->obExercicio->setNull( true );

        $this->obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();
        $this->obISelectMultiploEntidadeUsuario->setNull( true );

        $this->obTxtLicitacao = new TextBox();
        $this->obTxtLicitacao->setName     ( 'inCodLicitacao'   );
        $this->obTxtLicitacao->setRotulo   ( 'Licitação'        );
        $this->obTxtLicitacao->setTitle    ( 'Selecione a Licitação.' );

    }

    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente( $this->obExercicio );
        $obFormulario->addComponente( $this->obISelectMultiploEntidadeUsuario );
        $obFormulario->addComponente( $this->obTxtLicitacao );
    }

}
