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
	* Oculto do Formulario de Convenio TCEMG
	* Data de Criação   : 10/03/2014

	* @author Analista: Sergio Luiz dos Santos
	* @author Desenvolvedor: Michel Teixeira
	* @ignore

	$Id: OCManterConvenio.php 59612 2014-09-02 12:00:51Z gelson $

	*Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
require_once ( CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoPublicacaoConvenio.class.php' );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConvenio";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

function somaParticipacao($rsP)
{
    $nuSoma = 0.00;
    while ( !$rsP->eof() ) {
        $nuSoma += $rsP->getCampo ( 'nuPercentualParticipacao' );
        $rsP->proximo();
    }
    return $nuSoma;
}

function somaValorParticipantes($rs)
{
    $nuSoma = 0.00;
    while ( !$rs->eof() ) {
        $nuSoma += $rs->getCampo ( 'nuValorParticipacao' );
        $rs->proximo();
    }
    return $nuSoma;
}

function montaListaParticipantes($rsLista , $stJs = null)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $rsLista->addFormatacao( 'nuValorParticipacao', 'NUMERIC_BR' );

        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Participantes do Convênio " );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo 	( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    	( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo	( "Nome" );
        $obLista->ultimoCabecalho->setWidth 	( 60 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo	( "Tipo de Participação" );
        $obLista->ultimoCabecalho->setWidth		( 60 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo	( "Valor Participação" );
        $obLista->ultimoCabecalho->setWidth		( 60 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo	( "Participação " );
        $obLista->ultimoCabecalho->setWidth		( 60 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo	( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth		( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "stNomCgmParticipante" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "descricao_participacao" );
        $obLista->commitDado ();
        
        $obLista->addDado ();
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->ultimoDado->setCampo		( "nuValorParticipacao" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->ultimoDado->setCampo		( "[nuPercentualParticipacao] %" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao	( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao	( true );
        $obLista->ultimaAcao->setLink	( "JavaScript:alterarParticipante();" );
        $obLista->ultimaAcao->addCampo	( "inIndice1","inCgmParticipante" );
        $obLista->commitAcao ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao	( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao	( true );
        $obLista->ultimaAcao->setLink	( "JavaScript:excluirParticipante();" );
        $obLista->ultimaAcao->addCampo	( "inIndice1","inCgmParticipante" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnParticipantes').innerHTML = '".$stHTML."';\n";
    if ( $stJs )
        $js .= $stJs;

    return $js;
}

$inNumConvenio = $request->get('inNumConvenio');

switch ($request->get('stCtrl')) {
    case 'LimparSessao':
		Sessao::remove('participantes');
		Sessao::remove('arEmpenhos');
    break;


    case 'incluirParticipante':
		$arParticipante = Sessao::read('participantes');
		$inCgmParticipanteEditado = Sessao::read('inCgmParticipanteEditado');
		$stErro = '';

		if ( $request->get('inCgmParticipante') == '' ) {
			$stErro = 'Preencha o CGM do Participante!';
		} elseif ( $request->get('inCodTipoParticipante') == '' ) {
			$stErro = 'Selecione o tipo de Participação!';
		} elseif ( $request->get('nuValorParticipacao') == '' ) {
			$stErro = 'Preencha o Valor de Participação!';
		} elseif ( $request->get('stEsfera') == '' ) {
			$stErro = 'Selecione Esfera do Concedente!';
		}

		$inCgmParticipante          = $request->get('inCgmParticipante');
		$inCodTipoParticipante      = $request->get('inCodTipoParticipante');

		$nuValorParticipacao        = str_replace ( '.' , '' , $request->get('nuValorParticipacao')) ;
		$nuValorParticipacao        = str_replace ( ',' , '.' , $nuValorParticipacao ) ;

		$nuPercentualParticipacao   = $request->get('hdnPercentualParticipacao');
		$hdnPercentualParticipacao  = $request->get('hdnPercentualParticipacao');

		$nuValorConvenio            = str_replace ( '.' , '' , $request->get('nuValorConvenio') );
		$nuValorConvenio            = str_replace ( ',' , '.' , $nuValorConvenio );

		$nuSomaParticipantes        = $arParticipante ? somaValorParticipantes( $arParticipante ) : 0.00 ;
                
		$stEsfera                   = $request->get('stEsfera');

		$boAlteracao  = Sessao::read('boAlteracao');
		$nuValorAtual = Sessao::read('nuValorAtual');

		if ($boAlteracao == true) {
			$nuValorAtualTMP = str_replace ( ',' , '.' , $nuValorAtual) ;
			$tmp = number_format($nuValorAtualTMP, 2, '.', '');
			$nuSomaParticipantes = number_format($nuSomaParticipantes, 2, '.', '') - $tmp;
		}

		$valorTotalParticipantes = number_format($nuSomaParticipantes + $nuValorParticipacao,2,'.','');

		if (!$nuValorConvenio) {
			$stErro = "@<i>Valor do Convênio</i> deve ser informado antes da inclusão de participantes!";
		} else {
			if ($arParticipante ==  null) {
				$rsParticipantes = new Recordset;
			} else {
				$rsParticipantes = $arParticipante;
				$rsParticipantes->setPrimeiroElemento();

				while ( !$rsParticipantes->eof() ) {
				    if ( $rsParticipantes->getCampo('inCgmParticipante') == $inCgmParticipante ) {
				        if ( $boAlteracao != true )
				            $stErro = "@Participação do CGM ja incluida!";
				    }
				    $rsParticipantes->proximo();
				}
				$rsParticipantes->setPrimeiroElemento();
			}
		}

		if ($stErro != '') {
			echo "alertaAviso('" . $stErro . "','form','erro','".Sessao::getId()."');\n";
			echo " setTimeout('document.getElementById(\'inCgmParticipante\').focus();',400);\n";
		} else {
			// limpar campos
			$stJs  = "d.getElementById('stNomCgmParticipante').innerHTML = '&nbsp;'; \n";
			$stJs .= "d.getElementById('nuValorParticipacao').value = '0,00'; \n";
			$stJs .= "d.getElementById('hdnPercentualParticipacao').value = ''; \n";
			$stJs .= "d.getElementById('nuPercentualParticipacao').innerHTML = '0,00 %'; \n";
			$stJs .= "f.inCgmParticipante.value = '' ;\n";
			$stJs .= "$('btnIncluirParticipante').value    ='Incluir';   ";
			$stJs .= "f.stEsfera.value = '' ;\n";

			// buscar cgm
			require_once ( CAM_GA_CGM_NEGOCIO.'RCGM.class.php');
			$obCgm = new RCGM;
			$obCgm->setNumCGM ( $inCgmParticipante );
			$obCgm->consultar ( new Recordset );
			$stNomCgm = $obCgm->getNomCGM();
			unset ( $obCgm );

			// buscar tipo de participação
			require_once ( CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoTipoParticipante.class.php');
			$obTLicitacaoTipoParticipante = new TLicitacaoTipoParticipante;
			$obTLicitacaoTipoParticipante->setDado ( 'cod_tipo_participante' , $inCodTipoParticipante );
			$obTLicitacaoTipoParticipante->recuperaPorChave ( $rsTiposParticipante );
			$stDescricaoParticipacao = $rsTiposParticipante->getCampo ( 'descricao' );
			unset ( $obTLicitacaoTipoParticipante );

			// cria array a ser inserido
			$arParticipante = array  (
				                    'inCgmParticipante'         => $inCgmParticipante,
				                    'inCodTipoParticipante'     => $inCodTipoParticipante,
				                    'stNomCgmParticipante'      => $stNomCgm,
				                    'descricao_participacao'    => $stDescricaoParticipacao,
				                    'nuValorParticipacao'       => $nuValorParticipacao,
				                    'nuPercentualParticipacao'  => $nuPercentualParticipacao,
				                    'hdnPercentualParticipacao' => $hdnPercentualParticipacao,
				                    'stEsfera'                  => $stEsfera,
				                    );
			if ($boAlteracao == true) {
				while (!$rsParticipantes->eof()) {
					if ($rsParticipantes->getCampo( 'inCgmParticipante') != $inCgmParticipanteEditado) {
					    if ($rsParticipantes->getCampo( 'inCgmParticipante') == $inCgmParticipante) {
					        $stErro = "@Participação do CGM ja existe!";
					        break;
					    }
					}
					$rsParticipantes->proximo();
				}
				if ($stErro == '') {
					$rsParticipantes->setPrimeiroElemento();
					while (!$rsParticipantes->eof()) {
						if ($rsParticipantes->getCampo( 'inCgmParticipante') == $inCgmParticipanteEditado) {
						    $rsParticipantes->setCampo( 'inCgmParticipante'         , $arParticipante['inCgmParticipante']         );
						    $rsParticipantes->setCampo( 'inCodTipoParticipante'     , $arParticipante['inCodTipoParticipante']     );
						    $rsParticipantes->setCampo( 'stNomCgmParticipante'      , $arParticipante['stNomCgmParticipante']      );
						    $rsParticipantes->setCampo( 'descricao_participacao'    , $arParticipante['descricao_participacao']    );
						    $rsParticipantes->setCampo( 'nuValorParticipacao'       , $arParticipante['nuValorParticipacao']       );
						    $rsParticipantes->setCampo( 'nuPercentualParticipacao'  , str_replace('.',',',$arParticipante['nuPercentualParticipacao'])  );
						    $rsParticipantes->setCampo( 'hdnPercentualParticipacao' , $arParticipante['hdnPercentualParticipacao'] );
						    $rsParticipantes->setCampo( 'stEsfera'                  , $arParticipante['stEsfera']                  );
						    break;
						}
						$rsParticipantes->proximo();
					}
				} else {
					 echo "alertaAviso('" . $stErro . "','form','erro','".Sessao::getId()."');\n";
				}
			} else {
				$rsParticipantes->add( $arParticipante );
			}

			Sessao::remove('boAlteracao');
			Sessao::remove('nuValorAtual');
			Sessao::remove('nuPercentualAtual');
			$rsParticipantes->setPrimeiroElemento();
			Sessao::write('participantes',$rsParticipantes);
			echo montaListaParticipantes( $rsParticipantes , $stJs);
		}
	break;

    case 'limpaParticipante':
		limpaParticipante();
    break;

    case 'excluirParticipante':
        Sessao::remove('boAlteracao');
        Sessao::remove('nuValorAtual');
        Sessao::remove('nuPercentualAtual');
        $arParticipante = Sessao::read('participantes');
        $arParticipante = $arParticipante->arElementos;
        $arNovo = array();
        $numcgmExcluir = $_REQUEST['inCgmParticipante'];
        foreach ($arParticipante as $valor) {
            if ($valor[ 'inCgmParticipante' ] != $numcgmExcluir) {
                $arNovo[] = $valor;
            }
        }
        $rsParticipantes = new Recordset;
        $rsParticipantes->preenche ( $arNovo );
        Sessao::write('participantes',$rsParticipantes);
        echo montaListaParticipantes( $rsParticipantes );
    break;

    case 'atualizaParticipacao' :
            $focoConvenio = false;
            $rsParticipantes = Sessao::read('participantes');
            $somaParticipacoes = '';
            $stErro = '';

            if ($rsParticipantes != '') {
                $arParticipantes = $rsParticipantes->arElementos;
            } else {
                $arParticipantes = array();
            }

            if (is_array($arParticipantes) && count($arParticipantes) > 0) {
                foreach ($arParticipantes as $chave => $dadosParticipantes) {
                    $somaParticipacoes += str_replace('.','',$dadosParticipantes['nuValorParticipacao']);
                }
            }

            $nuValorParticipacao = str_replace ( "," , "." , $request->get('nuValorParticipacao') ) ;
            $nuValorConvenio = str_replace ( "," , "." , $request->get('nuValorConvenio') ) ;

            $nuValorParticipacao = trim(str_replace ( "." , "" , $nuValorParticipacao )) ;
            $nuValorConvenio = trim(str_replace ( "." , "" , $nuValorConvenio )) ;

            if ($nuValorConvenio != '') {
                if ( $nuValorParticipacao > $nuValorConvenio )
                    $stErro = "@<i>Valor de Participação</i> não pode ser maior que o <i>Valor do Convênio</i>";
                    $stJs = "d.getElementById('nuValorParticipacao').value = '".$request->get('nuValorConvenio')."'\n";
                if ( $nuValorParticipacao <= 0 )
                    $stErro = "@<i>Valor de Participação</i> deve ser maior que o 0( zero )";
                // calcula percentual
                if ($nuValorConvenio > 0) {
                    $percentual = $nuValorParticipacao * 100 / $nuValorConvenio ;
                }
                // soma participacao total
                if ( count (Sessao::read('participantes')) < 1  )
                    $nuSoma = 0.00;
                else
                    $nuSoma = somaParticipacao( Sessao::read('participantes') );
                if ( Sessao::read('nuPercentualAtual') )
                    $nuSoma -= Sessao::read('nuPercentualAtual');

                $percentual = number_format($percentual,2,'.','.');

                $boAlteracao = Sessao::read('boAlteracao');

                if ($boAlteracao) {
                    $hdnPercentualParticipacao = Sessao::read('nuPercentualAtual');
                    $hdnPercentualParticipacao = 1-($hdnPercentualParticipacao/100);

                    $nuOldValorParticipacao = $nuValorConvenio * $hdnPercentualParticipacao;
                    $somaParticipacoes =  $somaParticipacoes - $nuOldValorParticipacao;

                    $somaParticipacoes = $somaParticipacoes + $nuValorParticipacao;

                } else {
                    $somaParticipacoes = $somaParticipacoes + $nuValorParticipacao;
                }

            } else {
                $stErro = "@<i>Valor do Convênio</i> deve ser setado!";
                $focoConvenio = true;
            }
            if ($stErro == '') {
                $percentual = $nuValorParticipacao * 100 / $nuValorConvenio ;
                $bruto = $percentual;

                $percentual = number_format ( $percentual , 2 , ',' , '.') . " %";

                $percentual = str_replace('.',',',$percentual);

                $bruto      = number_format ( $bruto      , 2 , '.' , '') . "";
                $stJs  = "d.getElementById('hdnPercentualParticipacao').value = '".$bruto."'; \n";
                $stJs  .= "d.getElementById('nuPercentualParticipacao').innerHTML = '".$percentual."'; \n";
                echo $stJs;
            } else {
                $stJs  = "d.getElementById('nuValorParticipacao').value = '0,00'; \n";
                $stJs .= "d.getElementById('hdnPercentualParticipacao').value = ''; \n";
                $stJs .= "d.getElementById('nuPercentualParticipacao').innerHTML = '0,00 %'; \n";
                if ( $focoConvenio )
                    $stJs .= "f.nuValorConvenio.focus() ;\n";
                else
                    $stJs .= "f.inCgmParticipante.focus() ;\n";
                echo "alertaAviso('" . $stErro . "','form','erro','".Sessao::getId()."');\n";
                echo $stJs ;
            }
    break;

    case 'alterarParticipante':
        // participante
        $inCgmParticipante			= $request->get('inCgmParticipante');
        $inCgmParticipanteEditado	= $request->get('inCgmParticipante');

        // buscar no recordset
        $rsParticipantes = Sessao::read('participantes');

        while ( !$rsParticipantes->eof() ) {
            if ( $rsParticipantes->getCampo( 'inCgmParticipante' ) == $inCgmParticipante  ) {
                $stNomCgmParticipante = $rsParticipantes->getCampo( 'stNomCgmParticipante' );
                $nuValorParticipacao = number_format($rsParticipantes->getCampo( 'nuValorParticipacao' ) , 2 , ',' , '.' );

                $hdnPercentualParticipacao = $rsParticipantes->getCampo( 'hdnPercentualParticipacao' );

                $nuPercentualParticipacao = $rsParticipantes->getCampo( 'nuPercentualParticipacao' );

                $inCodTipoParticipante = $rsParticipantes->getCampo( 'inCodTipoParticipante' );
                
                $stEsfera = $rsParticipantes->getCampo( 'stEsfera' );
                break;
            }
            $rsParticipantes->proximo();
        }

        $hdnPercentualParticipacao 	= str_replace('.',',',$hdnPercentualParticipacao);
        $nuPercentualParticipacao 	= str_replace('.',',',$nuPercentualParticipacao );

        // carregar informações do participante
        $stJs  = "d.getElementById('stNomCgmParticipante').innerHTML    = '" . $stNomCgmParticipante . "';      \n";
        $stJs .= "d.getElementById('nuValorParticipacao').value         = '" . $nuValorParticipacao . "';       \n";
        $stJs .= "d.getElementById('hdnPercentualParticipacao').value   = '" . $hdnPercentualParticipacao . "';	\n";
        $stJs .= "d.getElementById('nuPercentualParticipacao').innerHTML= '" . $nuPercentualParticipacao . " %';\n";
        $stJs .= "f.inCgmParticipante.value                             = '" . $inCgmParticipante . "' ;        \n";
        $stJs .= "d.getElementById('stEsfera').value                    = '".$stEsfera."';                      \n";
        $stJs .= "jq('#btnIncluirParticipante').val('Alterar');                                                 \n";

        // seta na sessao que estamos alterando
        Sessao::write('boAlteracao'				,true						);
        Sessao::write('nuValorAtual'			,$nuValorParticipacao		);
        Sessao::write('nuPercentualAtual'		,$hdnPercentualParticipacao	);
        Sessao::write('inCgmParticipanteEditado',$inCgmParticipanteEditado	);
        echo $stJs ;
    break;

    case 'montaListas':
        $inNumConvenio	= $_REQUEST[ 'inNumConvenio' ];
        $inCodConvenio	= $_REQUEST[ 'inCodConvenio' ];
        $stExercicio 	= $_REQUEST[ 'stExercicio'	 ];
        $stJs="";
        
        if($inNumConvenio!='' && $inCodConvenio!='' && $stExercicio!='' ){
            include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConvenio.class.php' );
            $obTTCEMGConvenio = new TTCEMGConvenio();
            
            $stFiltro  = " WHERE nro_convenio   = ".$inNumConvenio;
            $stFiltro .= " AND exercicio        = '".$stExercicio."'";
            $stFiltro .= " AND cod_convenio     = ".$inCodConvenio;
            $obTTCEMGConvenio->recuperaTodos ( $rsConvenio, $stFiltro );
            if($rsConvenio->inNumLinhas==1){
                $stJs .= "f.cod_convenio.value = '".$rsConvenio->arElementos[0]['cod_convenio']."';";
                
                $stJs .= "f.cod_entidade.value = '".$rsConvenio->arElementos[0]['cod_entidade']."';";
                $stJs .= "f.inCodEntidade.value = '".$rsConvenio->arElementos[0]['cod_entidade']."';";
                $stJs .= "jQuery('#stNomEntidade option[value=".$rsConvenio->arElementos[0]['cod_entidade']."]').attr('selected', 'selected');\n";
                $stJs .= "f.inNumConvenio.value = '".$rsConvenio->arElementos[0]['nro_convenio']."';";
                $stJs .= "f.stExercicio.value = '".$rsConvenio->arElementos[0]['exercicio']."';";
                $stJs .= "f.inExercicio.value = '".$rsConvenio->arElementos[0]['exercicio']."';";
                $stJs .= "f.inExercicio.disabled = true; ";
                $stJs .= "f.dtAssinatura.value = '".$rsConvenio->arElementos[0]['data_assinatura']."';";
                $stJs .= "f.dtInicioExecucao.value = '".$rsConvenio->arElementos[0]['data_inicio']."';";
                $stJs .= "f.dtFinalVigencia.value = '".$rsConvenio->arElementos[0]['data_final']."';";
                $stJs .= "f.nuValorConvenio.value = '".number_format( $rsConvenio->arElementos[0]['vl_convenio'] , 2 , ',' , '.')."';";
                $stJs .= "f.nuValorContra.value = '".number_format( $rsConvenio->arElementos[0]['vl_contra_partida'] , 2 , ',' , '.')."';";
                $stJs .= "f.stObjeto.value = '".$rsConvenio->arElementos[0]['cod_objeto']."';";

                $where = " WHERE cod_objeto=".$rsConvenio->arElementos[0]['cod_objeto'];
                $desc_objeto = SistemaLegado::pegaDado('descricao', 'compras.objeto', $where);
                $stJs .= "d.getElementById('txtObjeto').innerHTML   = '".$desc_objeto."';\n";
                
                limpaParticipante();
                include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConvenioParticipante.class.php' );
                $obTTCEMGConvenioParticipante = new TTCEMGConvenioParticipante();
                $stFiltro  = " WHERE CP.cod_convenio   = ".$inCodConvenio;
                $stFiltro .= " AND CP.exercicio        = '".$stExercicio."'";
                $stFiltro .= " AND CP.cod_entidade     = ".$rsConvenio->arElementos[0]['cod_entidade'];
                $obTTCEMGConvenioParticipante->recuperaParticipante( $rsListaParticipante, $stFiltro );
                
                if($rsListaParticipante->inNumLinhas>0){
                    $rsParticipantes = new Recordset;
                    for($i=0;$i<$rsListaParticipante->inNumLinhas;$i++){
                        $arParticipante = array  (
                                            'inCgmParticipante'         => $rsListaParticipante->arElementos[$i][ 'cgm_participante'        ],
                                            'inCodTipoParticipante'     => $rsListaParticipante->arElementos[$i][ 'cod_tipo_participante'   ],
                                            'stNomCgmParticipante'      => $rsListaParticipante->arElementos[$i][ 'nom_cgm'                 ],
                                            'descricao_participacao'    => $rsListaParticipante->arElementos[$i][ 'descricao_participacao'  ],
                                            'nuValorParticipacao'       => $rsListaParticipante->arElementos[$i][ 'vl_concedido'            ],
                                            'nuPercentualParticipacao'  => $rsListaParticipante->arElementos[$i][ 'percentual'              ],
                                            'hdnPercentualParticipacao' => $rsListaParticipante->arElementos[$i][ 'percentual'              ],
                                            'stEsfera'                  => trim($rsListaParticipante->arElementos[$i][ 'esfera'             ]),
                                            );
                        $rsParticipantes->add( $arParticipante );
                    }
                    $rsParticipantes->setPrimeiroElemento();
                    Sessao::write('participantes',$rsParticipantes);
                    $stJs .= montaListaParticipantes ( $rsParticipantes );
                }
                
                include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConvenioEmpenho.class.php' );
                $obTTTCEMGConvenioEmpenho = new TTCEMGConvenioEmpenho();
                $stFiltro  = " WHERE CE.cod_convenio   = ".$inCodConvenio;
                $stFiltro .= " AND CE.exercicio        = '".$stExercicio."'";
                $stFiltro .= " AND CE.cod_entidade     = ".$rsConvenio->arElementos[0]['cod_entidade'];
                $obTTTCEMGConvenioEmpenho->recuperaConvenioEmpenho( $rsListaEmpenho, $stFiltro );
                
                if($rsListaEmpenho->inNumLinhas>0){
                    $arRegistro = array();
                    
                    for($i=0;$i<$rsListaEmpenho->inNumLinhas;$i++){
                        $arRegistro['cod_entidade'] = $rsListaEmpenho->arElementos[$i][ 'cod_entidade'      ];
                        $arRegistro['cod_empenho' ] = $rsListaEmpenho->arElementos[$i][ 'cod_empenho'       ];
                        $arRegistro['nom_cgm'     ] = $rsListaEmpenho->arElementos[$i][ 'nom_cgm'           ];
                        $arRegistro['exercicio'   ] = $rsListaEmpenho->arElementos[$i][ 'exercicio_empenho' ];
                        
                        $arEmpenhos[] = $arRegistro ;
                    }
                    Sessao::remove('arEmpenhos');
                    Sessao::write('arEmpenhos', $arEmpenhos);
                    $stJs .= montaListaEmpenhos();
                    $stJs .= "f.inCodEntidade.disabled = true;";
                    $stJs .= "f.stNomEntidade.disabled = true;";
                }
                
                include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConvenioAditivo.class.php' );
                $obTTCEMGConvenioAditivo = new TTCEMGConvenioAditivo();
                
                $stFiltro  = " WHERE cod_convenio   = ".$inCodConvenio;
                $stFiltro .= " AND exercicio        = '".$stExercicio."'";
                $stFiltro .= " AND cod_entidade     = ".$rsConvenio->arElementos[0]['cod_entidade'];
                $obTTCEMGConvenioAditivo->recuperaTodos ( $rsAditivo, $stFiltro );
                
                $arAditivoAux = array();
                
                while( !$rsAditivo->eof() ){
                    $arAditivoAux['inCodAditivo']           = $rsAditivo->getCampo('cod_aditivo');
                    $arAditivoAux['stDescAditivo']          = $rsAditivo->getCampo('descricao');
                    $arAditivoAux['dtAssinaturaAditivo']    = $rsAditivo->getCampo('data_assinatura');
                    $arAditivoAux['dtFinalAditivo']         = $rsAditivo->getCampo('data_final');
                    $arAditivoAux['nuValorAditivo']         = number_format( $rsAditivo->getCampo('vl_convenio') , 2 , ',' , '.');
                    $arAditivoAux['nuValorContraAditivo']   = number_format( $rsAditivo->getCampo('vl_contra') , 2 , ',' , '.');
                    
                    $arAditivo[] = $arAditivoAux;
                    $rsAditivo->proximo();
                }
                
                Sessao::remove('arAditivo');
                Sessao::write('arAditivo', $arAditivo);
                $stJs .= MontaListaAditivos();
            }
        }
        echo $stJs;
    break;
    
    case "limpaCampoEmpenho":
        $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
        $stJs .= "f.numEmpenho.value = '';";

        echo $stJs;
    break;

    case "preencheInner":
        if($_REQUEST['inCodEntidade'] and $_REQUEST['stExercicioEmpenho'] and $_REQUEST['numEmpenho']){
            include_once( CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenho.class.php' );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $stFiltro  = "   AND e.exercicio    = '".$_REQUEST[ 'stExercicioEmpenho' ]."'";
            $stFiltro .= "   AND e.cod_entidade =  ".$_REQUEST[ 'inCodEntidade' ];
            $stFiltro .= "   AND e.cod_empenho  =  ".$_REQUEST[ 'numEmpenho' 	];
            $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoCgm($rsRecordSet, $stFiltro);

            if($rsRecordSet->getNumLinhas() > 0){    
                $stJs  = 'd.getElementById("stEmpenho").innerHTML = "'.$rsRecordSet->getCampo('credor').'";';
            }else{
                $stJs  = "alertaAviso('Empenho inexistente.','form','erro','".Sessao::getId()."');\n";
                $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.numEmpenho.value = '';";
                $stJs .= "f.numEmpenho.focus();\n";
            }
        }else{
            if(!$_REQUEST['inCodEntidade']){
                $stJs  = "alertaAviso('Informe a entidade.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.inCodEntidade.focus();\n";
                $stJs .= "f.numEmpenho.value = '';";
            }
            if(!$_REQUEST['stExercicioEmpenho']){
                $stJs  = "alertaAviso('Informe o exercício do empenho.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.stExercicioEmpenho.focus();\n";
                $stJs .= "f.numEmpenho.value = '';";
            }
            if(!$_REQUEST['numEmpenho']){
                $stJs  = "alertaAviso('Informe o número do empenho.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.numEmpenho.focus();\n";
                $stJs .= "d.getElementById('stEmpenho').innerHTML = '&nbsp;';";
            }  
        }
        echo $stJs;
    break;

    case "incluirEmpenhoLista":
        $arRegistro = array();
        $arEmpenhos = array();
        $arRequest  = array();
        $arRequest  = explode('/', $_REQUEST['numEmpenho']);
        $boIncluir  = true; 
    
        $arEmpenhos = Sessao::read('arEmpenhos');

        if( $_REQUEST['stExercicioEmpenho'] and $arRequest[0] != "" and isset($_REQUEST['inCodEntidade']) and $_REQUEST['inCodEntidade']>0 ){
            include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConvenioEmpenho.class.php' );
            $obTTCEMGConvenioEmpenho = new TTCEMGConvenioEmpenho;
            $stFiltro  = " WHERE cod_empenho	=  ".$arRequest[0];
            $stFiltro .= " AND cod_entidade		=  ".$_REQUEST['inCodEntidade'];
            $stFiltro .= " AND exercicio_empenho= '".$_REQUEST['stExercicioEmpenho']."'";
            $obTTCEMGConvenioEmpenho->recuperaTodos($rsEmpenhoConvenio, $stFiltro);
 
            if($rsEmpenhoConvenio->getNumLinhas() == -1){
                include_once( CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenho.class.php' );
                $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
                $stFiltro  = " AND e.exercicio    = '".$_REQUEST['stExercicioEmpenho']."'";
                $stFiltro .= " AND e.cod_entidade =  ".$_REQUEST['inCodEntidade'];
                $stFiltro .= " AND e.cod_empenho  =  ".$arRequest[0];
                $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoCgm($rsRecordSet, $stFiltro);
 
                if( $rsRecordSet->getNumLinhas() > 0 ){
                    if( !SistemaLegado::comparaDatas($_REQUEST['dtInicioExecucao'],$rsRecordSet->getCampo('dt_empenho') )){
                        if( count( $arEmpenhos ) > 0 ){
                            foreach( $arEmpenhos as $key => $array ){
                                $stCod = $array['cod_empenho'];
                                $stEnt = $array['cod_entidade'];
        
                                if( $arRequest[0] == $stCod and $_REQUEST['inCodEntidade'] == $stEnt ){
                                    $boIncluir = false;
                                    $stJs .= "alertaAviso('Empenho já incluso na lista.','form','erro','".Sessao::getId()."');";
                                    break;
                                }
                            }
                        }
                        if( $boIncluir ){     
                            $arRegistro['cod_entidade'] = $rsRecordSet->getCampo('cod_entidade'	);
                            $arRegistro['cod_empenho' ] = $rsRecordSet->getCampo('cod_empenho'	);
                            $arRegistro['data_empenho'] = $rsRecordSet->getCampo('dt_empenho'	);
                            $arRegistro['nom_cgm'     ] = $rsRecordSet->getCampo('credor'		);
                            $arRegistro['exercicio'   ] = $rsRecordSet->getCampo('exercicio'	);
                            $arEmpenhos[] = $arRegistro ;
                                     
                            Sessao::write('arEmpenhos', $arEmpenhos);
                            $stJs .= "f.inCodEntidade.disabled = true; ";
                            $stJs .= "f.stNomEntidade.disabled = true; ";
                            $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                            $stJs .= "f.stEmpenho.value = '';";
                            $stJs .= "f.numEmpenho.value = '';";
                            $stJs .= "f.numEmpenho.focus();";
                            $stJs .= montaListaEmpenhos();
                        }
                    }else{
                        $stJs .= "alertaAviso('Início do período do convênio posterior a data do empenho.','form','erro','".Sessao::getId()."');";
                    }
                }else{
                    $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                    $stJs .= "f.stEmpenho.value = '';";
                    $stJs .= "f.numEmpenho.value = '';";
                    $stJs .= "f.numEmpenho.focus();";
                    $stJs .= "alertaAviso('Empenho informado inválido.','form','erro','".Sessao::getId()."');";
                }
            }else{
                $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.stEmpenho.value = '';";
                $stJs .= "f.numEmpenho.value = '';";
                $stJs .= "f.numEmpenho.focus();";
                $stJs .= "alertaAviso('Empenho já vinculado a um convênio.','form','erro','".Sessao::getId()."');";
            }
        }else{
            $stJs .= "alertaAviso('Informe a Entidade, o Número do Empenho e o Exercício.','form','erro','".Sessao::getId()."');";
        }
        echo $stJs;        
    break;

    case "limpar":
             $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
             $stJs .= "f.numEmpenho.value = '';";
             $stJs .= "f.numEmpenho.focus();";
        echo $stJs;
    break;

    case "excluirEmpenhoLista": 
        $arTempEmp = array();
        $arEmpenhos = Sessao::read('arEmpenhos');

        foreach ( $arEmpenhos as $registro ) {
            if ( $registro['cod_empenho'].$registro['cod_entidade'].$registro['exercicio'] != $_REQUEST['codEmpenho'].$_REQUEST['codEntidade'].$_REQUEST['stExercicio']  ) {
                $arTempEmp[] = $registro;
            }
        }

        if(count($arTempEmp) == 0){
            $stJs .= "f.inCodEntidade.disabled = false; ";
            $stJs .= "f.stNomEntidade.disabled = false; ";
        }

        Sessao::write('arEmpenhos', $arTempEmp);
        $stJs .= montaListaEmpenhos();

        echo $stJs;
    break;

    case "carregaEntidade":
        $stJs = "d.getElementById('cod_entidade').value   = '".$_REQUEST['inCodEntidade']."';\n";
        echo $stJs;
    break;

    case "incluirAditivoLista":
        $stJs = "";
        $arItem = array();
        $arAditivo = Sessao::read('arAditivo');
        $Ok = true;
        $mensagem = "";
        
        $arItem['inCodAditivo']         = $_REQUEST['inCodAditivo'];
        $arItem['stDescAditivo']        = $_REQUEST['stDescAditivo'];
        $arItem['dtAssinaturaAditivo']  = $_REQUEST['dtAssinaturaAditivo'];
        $arItem['dtFinalAditivo']       = $_REQUEST['dtFinalAditivo'];
        $arItem['nuValorAditivo']       = ($_REQUEST['nuValorAditivo']!='') ? $_REQUEST['nuValorAditivo'] : '0,00';
        $arItem['nuValorContraAditivo'] = ($_REQUEST['nuValorContraAditivo']!='') ? $_REQUEST['nuValorContraAditivo'] : '0,00';
        
        if($arItem['dtAssinaturaAditivo']!=''){
            $dtAssinaturaAditivo = explode('/',$arItem['dtAssinaturaAditivo']);
            $dtAssinaturaAditivo = strtotime($dtAssinaturaAditivo[2]."-".$dtAssinaturaAditivo[1]."-".$dtAssinaturaAditivo[0]);
            if($_REQUEST['dtAssinatura']!=''){
                $dtAssinatura = explode('/',$_REQUEST['dtAssinatura']);
                $dtAssinatura = strtotime($dtAssinatura[2]."-".$dtAssinatura[1]."-".$dtAssinatura[0]);
                if($dtAssinaturaAditivo<$dtAssinatura){
                    $mensagem .= "@Data de Assinatura do Aditivo Anterior a Data de Assinatura do Convênio.";
                    $Ok=false;
                }
            }else{
                $mensagem .= "@Informe a Data da Assinatura do Convênio.";
                $Ok=false;
            }
        }
        

        if($arItem['dtFinalAditivo']!=''){
            $dtTerminoAditivo = explode('/',$arItem['dtFinalAditivo']);
            $dtTerminoAditivo = $dtTerminoAditivo[2]."-".$dtTerminoAditivo[1]."-".$dtTerminoAditivo[0];
            if($_REQUEST['dtFinalVigencia']!=''){
                $dtFinal = explode('/',$_REQUEST['dtFinalVigencia']);
                $dtFinal = $dtFinal[2]."-".$dtFinal[1]."-".$dtFinal[0];
                if(strtotime($dtTerminoAditivo)<=strtotime($dtFinal)){
                    $mensagem .= "@Nova Data Final do Convênio Anterior ou Igual a Data Final do Convênio Original.";
                    $Ok=false;
                }
            }else{
                $mensagem .= "@Informe a Data do Final da Vigência do Convênio.";
                $Ok=false;  
            }
        }        
        
        for($i=0;$i<(count($arAditivo));$i++){
            if($arAditivo[$i]['inCodAditivo']==$arItem['inCodAditivo']){
                $Ok=false;
                $mensagem .= "@Aditivo(".$arItem['inCodAditivo'].") já incluso na lista.";
            }
        }
        
        if($Ok==false){
            $stJs .= "alertaAviso('".$mensagem."','form','erro','".Sessao::getId()."');";    
        }
        elseif($arItem['inCodAditivo']!=''&&$arItem['stDescAditivo']!=''&&$arItem['dtAssinaturaAditivo']!=''){         
                $arAditivo[]=$arItem;
                
                Sessao::write('arAditivo', $arAditivo);
                
                $stJs .= "f.inCodAditivo.value = '';";
                $stJs .= "f.stDescAditivo.value = '';";
                $stJs .= 'd.getElementById("stDescAditivo").innerHTML = "";';
                $stJs .= "f.dtAssinaturaAditivo.value = '".date('d/m/Y')."';";
                $stJs .= "f.dtFinalAditivo.value = '';";
                $stJs .= "f.nuValorAditivo.value = '';";
                $stJs .= "f.nuValorContraAditivo.value = '';";
                
                $stJs .= MontaListaAditivos();
        }else{            
            if($arItem['inCodAditivo']==''){
                $mensagem .= "@Informe o Número do Aditivo.";
            }
            if($arItem['stDescAditivo']==''){
                $mensagem .= "@Informe a Descrição da Alteração do Aditivo.";
            }
            if($arItem['dtAssinaturaAditivo']==''){
                $mensagem .= "@Informe a Data da Assinatura do Aditivo.";
            }
            
            $stJs .= "alertaAviso('".$mensagem."','form','erro','".Sessao::getId()."');";
        }
        echo $stJs; 
    break;

    case "excluirAditivoLista": 
        $stJs = "";
        $arTempAdit = array();
        $arAditivo = Sessao::read('arAditivo');

        foreach ( $arAditivo as $registro ) {
            if ( $registro['inCodAditivo'] != $_REQUEST['codAditivo'] ) {
                $arTempAdit[] = $registro;
            }
        }

        Sessao::write('arAditivo', $arTempAdit);
        
        $stJs .= MontaListaAditivos();

        echo $stJs;
    break;
}
    
function montaListaEmpenhos()
{
    $obLista = new Lista;
    $rsLista = new RecordSet;
    $rsLista->preenche ( Sessao::read('arEmpenhos') );

    $obLista->setRecordset( $rsLista );
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo ( 'Lista de empenhos' );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Entidade");
    $obLista->ultimoCabecalho->setWidth( 5);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Empenho");
    $obLista->ultimoCabecalho->setWidth( 10);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome do Credor");
    $obLista->ultimoCabecalho->setWidth( 80 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_entidade" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('excluirEmpenhoLista');" );
    $obLista->ultimaAcao->addCampo("","&codEmpenho=[cod_empenho]&codEntidade=[cod_entidade]&stExercicio=[exercicio]");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnLista').innerHTML = '';\n";
    if($rsLista->inNumLinhas>0){
        $stJs .= "d.getElementById('spnLista').innerHTML = '".$html."';\n";
    }

    return $stJs;
}
function limpaParticipante(){
        Sessao::remove('boAlteracao');
        Sessao::remove('nuValorAtual');
        Sessao::remove('nuPercentualAtual');
        $stJs  = "d.getElementById('stNomCgmParticipante').innerHTML    = '&nbsp;'; \n";
        $stJs .= "d.getElementById('nuValorParticipacao').value         = '0,00';   \n";
        $stJs .= "d.getElementById('hdnPercentualParticipacao').value   = '';       \n";
        $stJs .= "d.getElementById('nuPercentualParticipacao').innerHTML= '0,00 %'; \n";
        $stJs .= "f.inCgmParticipante.value                             = '';       \n";
        $stJs .= "f.stEsfera.value                                      = '';       \n";
        echo $stJs;
}
function MontaListaAditivos(){
    $stJs = "d.getElementById('spnListaAditivos').innerHTML = '';\n";
    $arAditivo=Sessao::read('arAditivo');    
    
    if(count($arAditivo)>0){
        Sessao::write('arAditivo', $arAditivo);
    
        $obLista = new Lista;
        $rsLista = new RecordSet;
        $rsLista->preenche ( $arAditivo );
            
        $obLista->setRecordset( $rsLista );
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo ( 'Lista de Aditivos' );
    
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();
    
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Número");
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descrição");
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor Contra-Partida");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
    
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inCodAditivo" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stDescAditivo" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
  
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nuValorAditivo" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nuValorContraAditivo" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
    
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 1 );
        $obLista->commitCabecalho();
        
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('excluirAditivoLista');" );
        $obLista->ultimaAcao->addCampo("","&codAditivo=[inCodAditivo]");
        $obLista->commitAcao();
    
        $obLista->montaHTML();
    
        $html = $obLista->getHTML();
        $html = str_replace("\n","",$html);
        $html = str_replace("  ","",$html);
        $html = str_replace("'","\\'",$html);
    
        
        if(count(Sessao::read('arAditivo'))>0){
            $stJs .= "d.getElementById('spnListaAditivos').innerHTML = '".$html."';\n";
        }
    }

    return $stJs;
}

?>