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
    * Pagina de processamento da Ata
    * Data de Criação: 23/01/2009
    *
    *
    * @author Analista:      Gelson Wolowski Gonçalvez <gelson.goncalves@cnm.org.br>
    * @author Desenvolvedor: Diogo Zarpelon            <diogo.zarpelon@cnm.org.br>
    *
    * @ignore

    $Id: PRManterAta.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC."TLicitacaoAta.class.php";
include_once TLIC."TLicitacaoComissao.class.php";
include_once TLIC."TLicitacaoPublicacaoAta.class.php";

# Definição do nome dos arquivos em PHP relacionados ao programa.
$stPrograma = "ManterAta";
$pgList     = "LS".$stPrograma.".php";
$pgGera     = "OCGera".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";

$stAcao = $request->get('stAcao');
$obErro = new Erro;

# Separa o código do edital do exercício.
list($inCodEdital, $stExercicioEdital) = explode('/', $_REQUEST['stNumEdital']);

$obTLicitacaoAta = new TLicitacaoAta;
Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTLicitacaoAta );

$obTLicitacaoPublicacaoAta = new TLicitacaoPublicacaoAta;

// Verifica se o edital já fora usado pelo usuário, para outra ata
$obTLicitacaoAta->setDado("num_edital",$inCodEdital);
$obTLicitacaoAta->setDado("exercicio",$stExercicioEdital);
$obTLicitacaoAta->recuperaAta($rsEditalAta);

// Recuperando da sessão todos os veículos incluídos
$arValores = Sessao::read('arValores');

if ($stAcao != 'alterar') {
    if ($rsEditalAta->getNumLinhas() > 0 ) {
        SistemaLegado::exibeAviso(urlencode("Edital (".$inCodEdital."/".$stExercicioEdital.") já esta vinculado a uma ata de encerramento!"),'n_incluir','erro');
        Sessao::encerraExcecao();
        exit;
    }
}

switch ($stAcao) {
    default :
        $inIdAta   = $_REQUEST['inIdAta'];
        $inNumAta  = $_REQUEST['inNumAta'];
        $stHoraAta = $_REQUEST['stHoraAta'];
        
        # Prepara as variáveis a serem inseridas na coluna timestamp da tabela licitacao.ata.
        list($inDia, $inMes, $inAno) = explode('/', $_REQUEST['dtDataAta']);
        
        $obAta= new TLicitacaoAta;
        $obAta->setDado('num_ata'       , $inNumAta                                  );
        $obAta->setDado('exercicio_ata' , Sessao::getExercicio()                     );
        $obAta->recuperaAta($rsAta);
        
        $dtResult = SistemaLegado::comparaDatas($_REQUEST['dtDataValidadeAta'], $_REQUEST['dtDataAta']);
        
        if ($dtResult <> 1) {
            SistemaLegado::exibeAviso("A data de validade da ata deve ser maior do que a data de publicação da ata!",'n_incluir','erro');
            break;
        }
        
        $obTLicitacaoAta->setDado('num_ata'         , $inNumAta                                     );
        $obTLicitacaoAta->setDado('exercicio_ata'   , Sessao::getExercicio()                        );
        $obTLicitacaoAta->setDado('num_edital'      , $inCodEdital                                  );
        $obTLicitacaoAta->setDado('exercicio'       , $stExercicioEdital                            );
        $obTLicitacaoAta->setDado('timestamp'       , $inAno.'-'.$inMes.'-'.$inDia.' '.$stHoraAta   );
        $obTLicitacaoAta->setDado('descricao'       , $_REQUEST['stDescricaoAta']                   );
        $obTLicitacaoAta->setDado('dt_validade_ata' , $_REQUEST['dtDataValidadeAta']                );
        $obTLicitacaoAta->setDado('tipo_adesao'     , $_REQUEST['cmbTipoAdesao']                    );
        
        if ($stAcao == 'incluir' && $rsAta->getNumLinhas() < 0 ) {
            $obTLicitacaoAta->proximoCod($inIdAta);
            $obTLicitacaoAta->setDado('id', $inIdAta);
            
            $obErro = $obTLicitacaoAta->inclusao();
            
        } elseif ($stAcao == 'alterar') {
            $obTLicitacaoAta->setDado('id', $inIdAta);
            
            $obErro = $obTLicitacaoAta->alteracao();
        } else {
                $obErro->setDescricao('Já possui este número de ata no sistema!');
        }
        
        if ( count( $arValores ) > 0 ) {
            foreach ($arValores as $arTemp) {
                if ( implode('',array_reverse(explode('/',$arTemp['dtDataPublicacao']))) < implode(array_reverse(explode('/',$_REQUEST['dtDataAta']))) ) {
                    $obErro->setDescricao('A data de publicação do veículo '.$arTemp['inVeiculo'].' deve ser maior ou igual a data da ata!');
                    break;
                }
            }
        }

        if (!$obErro->ocorreu()) {
            if ($stAcao == 'alterar') {
                // Primeiro deve excluir os veículos da sessão para incluí-los novamente
                $obTLicitacaoPublicacaoAta->setDado('ata_id', $inIdAta);
                $obErro = $obTLicitacaoPublicacaoAta->exclusao();
            }
            
            foreach ($arValores as $key => $value) {
                //Verifica próximo id de publicacao_ata
                $obTLicitacaoPublicacaoAta = new TLicitacaoPublicacaoAta;
                $obTLicitacaoPublicacaoAta->setCampoCod('id');
                $obTLicitacaoPublicacaoAta->proximoCod($idPublicacaoAta);
                    
                // Prepara as variáveis para serem inseridas na licitacao.publicacao_ata
                $obTLicitacaoPublicacaoAta->setDado('numcgm', $value['inVeiculo']);
                $obTLicitacaoPublicacaoAta->setDado('dt_publicacao', $value['dtDataPublicacao']);
                $obTLicitacaoPublicacaoAta->setDado('observacao', $value['stObservacao']);
                $obTLicitacaoPublicacaoAta->setDado('num_publicacao', $value['inNumPublicacao']);
                
                if ($stAcao == 'incluir' && $rsAta->getNumLinhas() < 0) {
                    $obTLicitacaoPublicacaoAta->setDado('ata_id', $inIdAta);
                    $obTLicitacaoPublicacaoAta->setDado('id', $idPublicacaoAta);
                    
                    $obErro = $obTLicitacaoPublicacaoAta->inclusao();
                    
                } elseif ($stAcao == 'alterar') {
                    $obTLicitacaoPublicacaoAta->setDado('ata_id', $inIdAta);
                    $obTLicitacaoPublicacaoAta->setDado('id', $idPublicacaoAta);
                    
                    // Primeiro deve excluir os veículos da sessão para incluí-los novamente
                    $obErro = $obTLicitacaoPublicacaoAta->inclusao();
                    
                }
            }
        }
        
        if (!$obErro->ocorreu()) {
            if ($stAcao == 'alterar') {
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&inIdAta=".$inIdAta."&stAcao=".$stAcao,"Ata: ".$inNumAta."/".Sessao::getExercicio(),"incluir","aviso", Sessao::getId(), "../");
                SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId()."&inIdAta=".$inIdAta."&stAcao=".$stAcao);
            } elseif ($stAcao == 'incluir' && $rsAta->getNumLinhas() < 0 ) {
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&inIdAta=".$inIdAta."&stAcao=".$stAcao,"Ata: ".$inNumAta."/".Sessao::getExercicio(),"incluir","aviso", Sessao::getId(), "../");
                SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId()."&inIdAta=".$inIdAta."&stAcao=".$stAcao);
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),'n_incluir','erro');
            }
            
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),'n_incluir','erro');
        }

    break;
}
Sessao::encerraExcecao();
